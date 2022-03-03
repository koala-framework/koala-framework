<?php
class Kwf_Controller_Action_Cli_Web_ComponentPagesMetaController extends Kwf_Controller_Action
{
    //internal
    public function rebuildWorkerAction()
    {
        set_time_limit(0);

        $queueFile = 'temp/pagemetaRebuildQueue';
        $statsFile = 'temp/pagemetaRebuildStats';

        $stats = unserialize(file_get_contents($statsFile));
        while (true) {
            //child process

            //echo "memory_usage (child): ".(memory_get_usage()/(1024*1024))."MB\n";
            if (memory_get_usage() > 128*1024*1024) {
                if ($this->_getParam('debug')) echo "new process...\n";
                break;
            }

            $queue = file_get_contents($queueFile);
            if (!$queue) break;

            $queue = explode("\n", $queue);
            if ($this->_getParam('debug')) echo "queued: ".count($queue).' :: '.round(memory_get_usage()/1024/1024, 2)."MB\n";
            $componentId = array_pop($queue);
            file_put_contents($queueFile, implode("\n", $queue));
            $stats['pages']++;

            if ($this->_getParam('debug')) echo "==> ".$componentId;
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId);
            if (!$page) {
                if ($this->_getParam('debug')) echo "$componentId not found!\n";
                continue;
            }
            if ($this->_getParam('debug')) echo " :: $page->url\n";
            if ($this->_getParam('verbose')) echo "getting child pages...";

            $childPages = $page->getChildPseudoPages(
                array('pageGenerator' => false),
                array('pseudoPage'=>false, 'unique'=>false) //don't recurse into unique boxes, causes endless recursion if box creates page
            );
            $childPages = array_merge($childPages, $page->getChildPseudoPages(
                array('pageGenerator' => true),
                array('pseudoPage'=>false)
            ));
            if ($this->_getParam('verbose')) echo " done\n";
            foreach ($childPages as $c) {
                if ($this->_getRecursiveSkip($c)) continue;
                if ($this->_getParam('verbose')) echo "queued $c->componentId\n";
                $queue[] = $c->componentId;
                file_put_contents($queueFile, implode("\n", $queue));
            }
            unset($c);

            $pageId = $page->componentId;
            unset($page);

            if ($this->_getParam('debug')) {
                //echo round(memory_get_usage()/1024/1024, 2)."MB";
                //echo " gen: ".Kwf_Component_Generator_Abstract::$objectsCount.', ';
                //echo " data: ".Kwf_Component_Data::$objectsCount.', ';
                //echo " row: ".Kwf_Model_Row_Abstract::$objectsCount.'';
            }
            //Kwf_Component_Data_Root::getInstance()->freeMemory();
            if ($this->_getParam('debug')) {
                //echo ' / '.round(memory_get_usage()/1024/1024, 2)."MB";
                //echo " gen: ".Kwf_Component_Generator_Abstract::$objectsCount.', ';
                //echo " data: ".Kwf_Component_Data::$objectsCount.', ';
                //echo " row: ".Kwf_Model_Row_Abstract::$objectsCount.'';
                //var_dump(Kwf_Model_Row_Abstract::$objectsByModel);
                //var_dump(Kwf_Component_Data::$objectsById);
                //echo "\n";
            }
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($pageId);
            if (!$page->isPage) continue;
            $model = Kwf_Component_PagesMetaModel::getInstance();
            $row = $model->getRow($page->componentId);
            if (!$row) {
                $row = $model->createRow();
                $row->changed_date = date('Y-m-d H:i:s');
            }
            $row->updateFromPage($page);
            $row->rebuilt = true;
            $row->changed_recursive = false;
            $row->save();
            $stats['addedPages']++;
            unset($page);

        }
        file_put_contents($statsFile, serialize($stats));
        if ($this->_getParam('debug')) echo "child finished\n";
        exit(0);
    }

    public function rebuildAction()
    {
        $m = Kwf_Component_PagesMetaModel::getInstance();
        $s = $m->select();
        $m->updateRows(array('rebuilt'=>0), $s);

        $startTime = microtime(true);
        $numProcesses = 0;

        $queueFile = 'temp/pagemetaRebuildQueue';
        $statsFile = 'temp/pagemetaRebuildStats';

        $componentId = 'root';
        if ($this->_getParam('componentId')) $componentId = $this->_getParam('componentId');
        file_put_contents($queueFile, $componentId);

        $stats = array(
            'pages' => 0,
            'addedPages' => 0,
        );
        file_put_contents($statsFile, serialize($stats));
        while (true) {
            $numProcesses++;
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php component-pages-meta rebuild-worker";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            if ($this->_getParam('verbose')) $cmd .= " --verbose";
            passthru($cmd, $status);

            if ($status != 0) {
                throw new Kwf_Exception("child process failed");
            }

            if ($this->_getParam('debug')) echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
            if (!file_get_contents($queueFile)) {
                if ($this->_getParam('debug')) echo "fertig.\n";
                break;
            }
        }

        if ($this->_getParam('debug')) {
            $secondsAsDurationHelper = new Kwf_View_Helper_SecondsAsDuration();
            $stats = unserialize(file_get_contents($statsFile));
            echo "fulltext reindex finished.\n";
            echo "duration: ".$secondsAsDurationHelper->secondsAsDuration(microtime(true)-$startTime)."s\n";
            echo "used child processes: $numProcesses\n";
            echo "processed pages: $stats[pages]\n";
            echo "indexed pages: $stats[addedPages]\n";
        }

        //delete rows that have not been rebuilt
        $m = Kwf_Component_PagesMetaModel::getInstance();
        $s = $m->select();
        $s->whereEquals('rebuilt', false);
        $m->deleteRows($s);

        exit;
    }

    public function updateChangedJobAction()
    {
        $start = microtime(true);
        $m = Kwf_Component_PagesMetaModel::getInstance();
        $s = $m->select();
        $s->whereEquals('changed_recursive', true);
        foreach ($m->getRows($s) as $row) {
            if ($this->_getParam('debug')) echo "changed recursive: $row->page_id\n";
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($row->page_id);
            if (!$page) {
                $row->deleteRecursive();
                continue;
            }
            $this->_processRecursive($page);
            $row->changed_recursive = false;
            $row->save();
            if (microtime(true) - $start > 30) {
                if ($this->_getParam('debug')) echo "stopped after ".round(microtime(true) - $start)."sec\n";
                break;
            }
        }
        exit;
    }

    private function _processRecursive(Kwf_Component_Data $page)
    {
        if (memory_get_usage() > 128*1024*1024) {
            if ($this->_getParam('debug')) echo "Collect garbage...\n";;
            Kwf_Component_Data_Root::getInstance()->freeMemory();
        }

        if ($this->_getParam('debug')) echo "processing changed_recursive $page->componentId\n";
        $childPages = $page->getChildPseudoPages(
            array('pageGenerator' => false),
            array('pseudoPage'=>false, 'unique'=>false) //don't recurse into unique boxes, causes endless recursion if box creates page
        );
        $ret = array();
        foreach ($childPages as $p) {
            if ($this->_getRecursiveSkip($p)) continue;
            $m = Kwf_Component_PagesMetaModel::getInstance();
            $r = $m->getRow($p->componentId);
            if (!$r) {
                $r = $m->createRow();
            }
            $r->updateFromPage($p);
            $r->changed_date = date('Y-m-d H:i:s');
            $r->save();
            $this->_processRecursive($p);
        }
    }

    private function _getRecursiveSkip(Kwf_Component_Data $page)
    {
        if (Kwc_Abstract::getFlag($page->componentClass, 'skipPagesMeta')) {
            return true;
        }
        $c = $page->parent;
        while ($c) {
            if (Kwc_Abstract::getFlag($c->componentClass, 'skipPagesMeta')) {
                return true;
            }
            $c = $c->parent;
        }
        return false;
    }
}
