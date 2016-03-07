<?php
class Kwf_Controller_Action_Cli_Web_FulltextController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "various fulltext index commands";
    }

    public function optimizeAction()
    {
        Kwf_Util_MemoryLimit::set(512);
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
        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subroot'), array('ignoreVisible' => true));
        if (!$subroot) $subroot = Kwf_Component_Data_Root::getInstance();

        $pagesMetaModel = Kwf_Component_PagesMetaModel::getInstance();

        $documentIds = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getAllDocumentIds($subroot);
        $i = 0;
        foreach ($documentIds as $documentId) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('page_id', $documentId);
            $s->whereEquals('fulltext_skip', false);
            $s->whereEquals('deleted', false);
            if ($pagesMetaModel->countRows($s) == 0) {
                if (!$this->_getParam('silent')) {
                    echo "\n$documentId ist im index aber nicht im Seitenbaum, wird gelöscht...\n";
                }
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($subroot, $documentId);
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

    public function rebuildAction()
    {
        if (!$this->_getParam('skip-page-meta')) {
            $job = new Kwc_Root_MaintenanceJobs_PageMetaRebuild();
            $job->execute(true);
        }

        $job = new Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_CheckContents(array(
            'skipDiff' => true
        ));
        $job->execute(true);
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

    public function updateChangedJobAction()
    {
        $start = microtime(true);
        $pagesMetaModel = Kwf_Component_PagesMetaModel::getInstance();
        $s = $pagesMetaModel->select();
        $s->where(new Kwf_Model_Select_Expr_Higher('changed_date', new Kwf_DateTime(time() - 5*60))); //>5min ago (for buffering!)
        $s->whereEquals('fulltext_skip', false);
        $s->where('changed_date > fulltext_indexed_date OR ISNULL(fulltext_indexed_date)');
        $countIndexed = 0;
        foreach ($pagesMetaModel->getRows($s) as $row) {
            $countIndexed++;
            if ($this->_getParam('debug')) echo "changed: $row->page_id\n";
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($row->page_id);
            if (!$page) {
                if ($this->_getParam('debug')) echo "deleting $row->page_id\n";
                $sr = Kwf_Component_Data_Root::getInstance()->getComponentById($row->subroot_component_id, array('ignoreVisible' => true));
                if ($sr) {
                    Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($sr, $row->page_id);
                }
                $row->fulltext_indexed_date = date('Y-m-d H:i:s');
                $row->save();
            } else {
                if ($this->_getParam('debug')) echo "indexing $page->componentId\n";
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page);
            }
            unset($page);
            if (microtime(true) - $start > 30) {
                if ($this->_getParam('debug')) echo "stopped after ".round(microtime(true) - $start)."sec\n";
                break;
            }
        }

        if ($countIndexed > 0) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext optimize";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }

        exit;
    }

    public function checkContentsSubrootAction()
    {
        Kwf_Util_MemoryLimit::set(256);

        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subroot'));
        if (!$subroot) $subroot = Kwf_Component_Data_Root::getInstance();
        $i = 0;

        $stats = array(
            'indexedPages' => 0,
            'diffPages' => 0,
        );
        $pagesMetaModel = Kwf_Component_PagesMetaModel::getInstance();
        $select = new Kwf_Model_Select();
        $select->whereEquals('deleted', false);
        $select->whereEquals('fulltext_skip', false);
        $select->whereEquals('subroot_component_id', $subroot->componentId);
        $it = new Kwf_Model_Iterator_Packages(
            new Kwf_Model_Iterator_Rows($pagesMetaModel, $select)
        );
        if ($this->_getParam('debug')) {
            $it = new Kwf_Iterator_ConsoleProgressBar($it);
        }
        foreach ($it as $row) {
            $componentId = $row->page_id;
            $page = Kwf_Component_Data_Root::getInstance()->getComponentById($componentId);
            if (!$this->_getParam('skip-diff')) {
                $docContent = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getDocumentContent($page);
            } else {
                $docContent = '';
            }
            $newDoc = Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getFulltextContentForPage($page);
            if (!$newDoc) {
                //this can happen (if there is no content)
                Kwf_Util_Fulltext_Backend_Abstract::getInstance()->deleteDocument($subroot, $componentId);
                $row = $pagesMetaModel->getRow($componentId);
                if ($row) $row->delete();
                continue;
            }
            if (trim($newDoc['content']) != trim($docContent)) {
                $stats['diffPages']++;
                if (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->indexPage($page)) {
                    $stats['indexedPages']++;
                }
                if (!$this->_getParam('silent') && !$this->_getParam('skip-diff')) echo "DIFF: $componentId\n";
            }
            unset($page);
            if ($i++ % 10) {
                Kwf_Component_Data_Root::getInstance()->freeMemory();
            }
            //if ($this->_getParam('debug')) echo "memory_usage ".(memory_get_usage()/(1024*1024))."MB\n";
        }

        if ($this->_getParam('debug')) {
            if (!$this->_getParam('skip-diff')) echo "pages with diff: $stats[diffPages]\n";
            echo "indexed pages: $stats[indexedPages]\n";
        }
        exit;
    }

    public function startSolrAction()
    {
        $path = Kwf_Config::getValue('fulltext.solr.startServerPath');
        if (!$path) {
            throw new Kwf_Exception_Client("Solr is not ment to be started manually from cli on this section.");
        }

        $solrHome = getcwd().'/solr';
        chdir($path);
        $cmd = "java -Dsolr.solr.home=$solrHome -jar start.jar";
        passthru($cmd, $ret);
        exit($ret);
    }
}
