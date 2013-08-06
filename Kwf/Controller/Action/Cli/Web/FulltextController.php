<?php
class Kwf_Controller_Action_Cli_Web_FulltextController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "various fulltext index commands";
    }

    public function optimizeAction()
    {
        ini_set('memory_limit', '512M');
        if ($this->_getParam('debug')) echo "\noptimize index...\n";
        Kwf_Util_Fulltext_Backend_Abstract::getInstance()->optimize($this->_getParam('debug'));
        if ($this->_getParam('debug')) echo "done.\n";
        exit;
    }

    public function checkForInvalidAction()
    {
        if ($this->_getParam('debug')) echo "check for invalid entries...\n";
        foreach (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getSubroots() as $subroot) {
            if ($this->_getParam('debug')) echo "$subroot\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-for-invalid-subroot --subroot=$subroot";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }

        $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext optimize";
        if ($this->_getParam('debug')) $cmd .= " --debug";
        system($cmd);

        exit;
    }

    public function checkForInvalidSubrootAction()
    {
        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subroot'));
        if (!$subroot) $subroot = Kwf_Component_Data_Root::getInstance();

        $documentIds = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getAllDocumentIds($subroot);
        $i = 0;
        foreach ($documentIds as $documentId) {
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($documentId);
            if ($page && Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) $page = null;
            $c = $page;
            while($c && $c = $c->parent) {
                if (Kwc_Abstract::getFlag($c->componentClass, 'skipFulltextRecursive')) {
                    $page = null;
                    break;
                }
            }
            if (!$page) {
                if (!$this->_getParam('slient')) {
                    echo "\n$documentId ist im index aber nicht im Seitenbaum, wird gelöscht...\n";
                }
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($subroot, $documentId);
                $m = Kwc_FulltextSearch_MetaModel::getInstance();
                $row = $m->getRow($documentId);
                if ($row) {
                    $row->delete();
                }
            }
            unset($page);
            if ($i++ % 10) {
                Kwf_Component_Data_Root::getInstance()->freeMemory();
            }
        }
        exit;
    }

    private static function _getAllPossiblePageComponentClasses()
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwf_Component_Generator_Abstract::getInstances($class, array('pseudoPage'=>true)) as $g) {
                foreach ($g->getChildComponentClasses() as $c) {
                    if (!in_array($c, $ret)) {
                        $ret[] = $c;
                    }
                }
            }
        }
        return $ret;
    }

    private static function _canHaveFulltext($class)
    {
        static $cache = array();
        if (isset($cache[$class])) return $cache[$class];
        $cache[$class] = false;
        if (Kwc_Abstract::getFlag($class, 'skipFulltext')) {
            return $cache[$class]; //false
        }
        if (Kwc_Abstract::getFlag($class, 'hasFulltext')) {
            $cache[$class] = true;
            return $cache[$class];
        }
        foreach (Kwc_Abstract::getChildComponentClasses($class) as $c) {
            if (self::_canHaveFulltext($c)) {
                $cache[$class] = true;
                return $cache[$class];
            }
        }
        return $cache[$class]; //false
    }

    public function deleteAllAction()
    {
        foreach (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getSubroots() as $sr) {
            $sr = Kwf_Component_Data_Root::getInstance()->getComponentById($sr);
            if (!$sr) $sr = Kwf_Component_Data_Root::getInstance();
            echo "deleting ALL documents for subroot $sr->componentId ";
            try {
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteAll($sr);
            } catch (Exception $e) {
                echo "[ERROR {$e->getMessage()}]\n";
                continue;
            }
            echo "[OK]\n";
        }
        exit;
    }

    //internal
    public function rebuildWorkerAction()
    {
        ini_set('memory_limit', '512M');


        $pageClassesThatCanHaveFulltext = array();
        foreach (self::_getAllPossiblePageComponentClasses() as $c) {
            if (self::_canHaveFulltext($c)) {
                $pageClassesThatCanHaveFulltext[] = $c;
            }
        }

        $queueFile = 'temp/fulltextRebuildQueue';
        $statsFile = 'temp/fulltextRebuildStats';

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
            $componentId = array_shift($queue);
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
                array('pageGenerator' => false, 'componentClasses'=>$pageClassesThatCanHaveFulltext),
                array('pseudoPage'=>false)
            );
            $childPages = array_merge($childPages, $page->getChildPseudoPages(
                array('pageGenerator' => true),
                array('pseudoPage'=>false)
            ));
            if ($this->_getParam('verbose')) echo " done\n";
            foreach ($childPages as $c) {

                $i = $c;
                do {
                    if (Kwc_Abstract::getFlag($i->componentClass, 'skipFulltextRecursive')) {
                        continue 2;
                    }
                } while($i = $i->parent);

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
                //p(Kwf_Component_ModelObserver::getInstance()->getProcess());
                //var_dump(Kwf_Model_Row_Abstract::$objectsByModel);
                //var_dump(Kwf_Component_Data::$objectsById);
                //echo "\n";
            }
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($pageId);
            if (!$page->isPage) continue;
            if (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page, !!$this->_getParam('verbose'))) {
                $stats['indexedPages']++;
            }
            unset($page);

        }
        file_put_contents($statsFile, serialize($stats));
        if ($this->_getParam('debug')) echo "child finished\n";
        exit(0);
    }

    public function rebuildAction()
    {
        ini_set('memory_limit', '512M');
        if (!$this->_getParam('skip-check-for-invalid')) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-for-invalid";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }

        $startTime = microtime(true);
        $numProcesses = 0;

        $queueFile = 'temp/fulltextRebuildQueue';
        $statsFile = 'temp/fulltextRebuildStats';

        $componentId = 'root';
        if ($this->_getParam('componentId')) $componentId = $this->_getParam('componentId');
        file_put_contents($queueFile, $componentId);

        $stats = array(
            'pages' => 0,
            'indexedPages' => 0,
        );
        file_put_contents($statsFile, serialize($stats));
        while(true) {
            $numProcesses++;
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext rebuild-worker";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd, $status);

            if ($status != 0) {
                throw new Kwf_Exception("child process failed");
            }

            if ($this->_getParam('debug')) echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
            if (!file_get_contents($queueFile)) {
                if ($this->_getParam('debug')) echo "fertig.\n";
                break;
            }
        }

        if (!$this->_getParam('silent')) {
            $stats = unserialize(file_get_contents($statsFile));
            echo "fulltext reindex finished.\n";
            echo "duration: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(microtime(true)-$startTime)."s\n";
            echo "used child processes: $numProcesses\n";
            echo "processed pages: $stats[pages]\n";
            echo "indexed pages: $stats[indexedPages]\n";
        }

        if (!$this->_getParam('skip-optimize')) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext optimize";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }
        exit;
    }

    public function searchAction()
    {
        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subroot'));
        $queryStr = $this->_getParam('query');

        $start = microtime(true);
        /*
        if ($this->_getParam('news')) {
            $pathTerm  = new Zend_Search_Lucene_Index_Term('kwcNews', 'kwcNews');
            $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
            $query->addSubquery($pathQuery, true);
        }
        */
        $hits = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->search($subroot, $queryStr);
        echo "searched in ".(microtime(true)-$start)."s\n";
        foreach ($hits as $hit) {
            //echo "score ".$hit['score']."\n";
            echo "  componentId: ".$hit['componentId']."\n";
            echo "\n";
        }
        exit;
    }

    private function _processRecursive(Kwf_Component_Data $page)
    {
        static $pageClassesThatCanHaveFulltext;
        if (!isset($pageClassesThatCanHaveFulltext)) {
            $pageClassesThatCanHaveFulltext = array();
            foreach (self::_getAllPossiblePageComponentClasses() as $c) {
                if (self::_canHaveFulltext($c)) {
                    $pageClassesThatCanHaveFulltext[] = $c;
                }
            }
        }

        if ($this->_getParam('debug')) echo "processing changed_recursive $page->componentId\n";
        $childPages = $page->getChildPseudoPages(
            array('pageGenerator' => false, 'componentClasses'=>$pageClassesThatCanHaveFulltext),
            array('pseudoPage'=>false)
        );
        $ret = array();
        foreach ($childPages as $p) {
            $m = Kwc_FulltextSearch_MetaModel::getInstance();
            $s = $m->select()
                ->whereEquals('page_id', $p->componentId);
            $r = $m->getRow($s);
            if (!$r) {
                $r = $m->createRow();
                $r->page_id = $p->componentId;
            }
            $r->changed_date = date('Y-m-d H:i:s');
            $r->save();
            $this->_processRecursive($p);
        }
    }

    public function updateChangedAction()
    {
        $start = microtime(true);
        $m = Kwc_FulltextSearch_MetaModel::getInstance();
        $s = $m->select();
        $s->where(new Kwf_Model_Select_Expr_Higher('changed_date', new Kwf_DateTime(time() - 5*60))); //>5min ago (for buffering!)
        //$s->where(new Kwf_Model_Expr_Not(new Kwf_Model_Expr_Equals('changed_date', 'indexed_date')));
        $s->where('changed_date > indexed_date OR ISNULL(indexed_date)');
        foreach ($m->getRows($s) as $row) {
            if ($this->_getParam('debug')) echo "changed: $row->page_id\n";
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($row->page_id);
            if (!$page) {
                //we don't know the correct subroot, so try deleting from all subroots
                foreach (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getSubroots() as $sr) {
                    $sr = Kwf_Component_Data_Root::getInstance()->getComponentById($sr);
                    if (!$sr) $sr = Kwf_Component_Data_Root::getInstance();
                    Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($sr, $row->page_id);
                }
                $row->delete();
                continue;
            }
            if (!$page->isPage) continue;
            if ($row->changed_recursive) {
                $row->changed_recursive = false;
                $this->_processRecursive($page);
            } else {
                if ($this->_getParam('debug')) echo "indexing $page->componentId\n";
                if (!Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page)) {
                    //does have no fulltext content
                    $row->changed_date = null;
                    $row->indexed_date = null;
                }
                unset($page);
            }
            $row->save();
            if (microtime(true) - $start > 30) {
                if ($this->_getParam('debug')) echo "stopped after ".round(microtime(true) - $start)."sec\n";
                break;
            }
        }

        $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext optimize";
        if ($this->_getParam('debug')) $cmd .= " --debug";
        system($cmd);

        exit;
    }

    public function checkContentsAction()
    {
        $startTime = microtime(true);

        foreach (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getSubroots() as $subroot) {

            $t = time();
            if (!$this->_getParam('silent')) echo "\n[$subroot] check-for-invalid...\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-for-invalid-subroot --subroot=$subroot";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            if ($this->_getParam('silent')) $cmd .= " --silent";
            passthru($cmd, $ret);
            if ($ret) exit($ret);
            if (!$this->_getParam('silent')) echo "[$subroot] check-for-invalid finished: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(time()-$t)."\n\n";

            $t = time();
            if (!$this->_getParam('silent')) echo "\n[$subroot] check-pages...\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-pages-subroot ";
            if ($subroot) {
                $cmd .= "--componentId=$subroot";
            } else {
                $cmd .= "--componentId=root";
            }
            if ($this->_getParam('debug')) $cmd .= " --debug";
            if ($this->_getParam('silent')) $cmd .= " --silent";
            passthru($cmd, $ret);
            if ($ret) exit($ret);
            if (!$this->_getParam('silent')) echo "[$subroot] check-pages finished: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(time()-$t)."\n\n";

            $t = time();
            if (!$this->_getParam('silent')) echo "\n[$subroot] check-contents...\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-contents-subroot --subroot=$subroot";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            if ($this->_getParam('silent')) $cmd .= " --silent";
            passthru($cmd, $ret);
            if ($ret) exit($ret);
            if (!$this->_getParam('silent')) echo "[$subroot] check-contents finished: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(time()-$t)."\n\n";

            $t = time();
            if (!$this->_getParam('silent')) echo "\n[$subroot] optimize...\n";
            Kwf_Util_Fulltext_Backend_Abstract::getInstance()->optimize($this->_getParam('debug'));
            if (!$this->_getParam('silent')) echo "[$subroot] optimize finished: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(time()-$t)."\n\n";
        }

        if (!$this->_getParam('silent')) echo "\ncomplete fulltext check-contents finished: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(microtime(true)-$startTime)."s\n";
        exit;
    }

    public function checkContentsSubrootAction()
    {
        ini_set('memory_limit', '256M');

        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subroot'));
        if (!$subroot) $subroot = Kwf_Component_Data_Root::getInstance();
        $documents = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getAllDocuments($subroot);
        if ($this->_getParam('debug')) echo "count: ".count($documents)."\n";
        $i = 0;

        $stats = array(
            'indexedPages' => 0,
            'diffPages' => 0,
        );
        foreach ($documents as $componentId=>$doc) {
            if ($this->_getParam('debug')) echo "checking: $i: $componentId\n";;
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId);
            if (!$page) continue;
            if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) $page = null;
            if (!$page) continue; //should not happen
            if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltextRecursive')) $page = null;
            if (!$page) continue; //should not happen
            $newDoc = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getFulltextContentForPage($page);
            if (!$newDoc) {
                //this can happen (if there is no content)
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($subroot, $componentId);
                $row = Kwc_FulltextSearch_MetaModel::getInstance()->getRow($componentId);
                if ($row) $row->delete();
                continue;
            }
            if (trim($newDoc['content']) != trim($doc['content'])) {
                $stats['diffPages']++;
                if (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page)) {
                    $stats['indexedPages']++;
                }
                if (!$this->_getParam('silent')) echo "DIFF: $componentId\n";
            }
            unset($page);
            if ($i++ % 10) {
                Kwf_Component_Data_Root::getInstance()->freeMemory();
            }
            //if ($this->_getParam('debug')) echo "memory_usage ".(memory_get_usage()/(1024*1024))."MB\n";
        }

        if (!$this->_getParam('silent')) {
            echo "pages with diff: $stats[diffPages]\n";
            echo "indexed pages: $stats[indexedPages]\n";
        }
        exit;
    }

    public function checkPagesSubrootAction()
    {
        ini_set('memory_limit', '256M');

        $pageClassesThatCanHaveFulltext = array();
        foreach (self::_getAllPossiblePageComponentClasses() as $c) {
            if (self::_canHaveFulltext($c)) {
                $pageClassesThatCanHaveFulltext[] = $c;
            }
        }


        $startTime = microtime(true);

        if (!$this->_getParam('componentId')) throw new Kwf_Exception_Client("componentId parameter required");

        $componentId = $this->_getParam('componentId');
        $queue = array($componentId);

        $stats = array(
            'pages' => 0,
            'indexedPages' => 0
        );
        while ($queue) {

            if (!$queue) break;

            if ($this->_getParam('debug')) echo "queued: ".count($queue).' :: '.round(memory_get_usage()/1024/1024, 2)."MB\n";
            $componentId = array_shift($queue);
            $stats['pages']++;

            //if ($this->_getParam('debug')) echo "==> ".$componentId;
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId);
            //if ($this->_getParam('debug')) echo " :: $page->url\n";
            if (!$page) {
                if ($this->_getParam('debug')) echo "$componentId not found!\n";
                continue;
            }
            //echo "$page->url\n";
            if ($this->_getParam('verbose')) echo "getting child pages...";

            $childPages = $page->getChildPseudoPages(
                array('pageGenerator' => false, 'componentClasses'=>$pageClassesThatCanHaveFulltext),
                array('pseudoPage'=>false)
            );
            $childPages = array_merge($childPages, $page->getChildPseudoPages(
                array('pageGenerator' => true),
                array('pseudoPage'=>false)
            ));
            if ($this->_getParam('verbose')) echo " done\n";
            foreach ($childPages as $c) {
                $i = $c;
                do {
                    if (Kwc_Abstract::getFlag($i->componentClass, 'skipFulltextRecursive')) {
                        continue 2;
                    }
                } while($i = $i->parent);
                if ($this->_getParam('verbose')) echo "queued $c->componentId\n";
                $queue[] = $c->componentId;
            }
            unset($c);

            $hasFulltext = false;
            if (!Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext') &&
                $page->isPage &&
                ($page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false)) || Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext'))
            ) {
                $hasFulltext = true;
            }

            if ($hasFulltext) {
                if (!Kwf_Util_Fulltext_Backend_Abstract::getInstance()->documentExists($page)) {
                    if (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page, $this->_getParam('debug'))) {
                        $stats['indexedPages']++;
                        if (!$this->_getParam('silent')) echo "not found in index: $page->componentId has content!!!!\n";
                    } else {
                        if ($this->_getParam('debug')) echo "not found in index: $page->componentId has NO content (that's ok)\n";
                    }
                }
            }
            unset($page);

            if ($stats['pages'] % 50 == 0) {
                Kwf_Component_Data_Root::getInstance()->freeMemory();
            }
        }

        if (!$this->_getParam('silent')) {
            echo "processed pages: $stats[pages]\n";
            echo "indexed pages: $stats[indexedPages]\n";
        }
        exit;
    }

    public function startSolrAction()
    {
        if (!Kwf_Config::getValue('fulltext.solr.allowStart')) {
            throw new Kwf_Exception_Client("Solr is not ment to be started manually from cli on this section.");
        }

        $solrHome = getcwd().'/solr';
        chdir(Kwf_Config::getValue('externLibraryPath.solrServer'));
        $cmd = "java -Dsolr.solr.home=$solrHome -jar start.jar";
        passthru($cmd, $ret);
        exit($ret);
    }
}
