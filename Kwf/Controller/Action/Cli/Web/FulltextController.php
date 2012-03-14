<?php
class Kwf_Controller_Action_Cli_Web_FulltextController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "various fulltext index commands";
    }

    public function termsAction()
    {
        //d(Kwf_Util_Fulltext::getInstance()->terms());
        $i = Kwf_Util_Fulltext::getInstance();
        $i->resetTermsStream();
        $i->skipTo(new Zend_Search_Lucene_Index_Term('w', 'title'));
        while ($i->currentTerm()) {
            p($i->currentTerm());
            $i->nextTerm();
        }
        $i->closeTermsStream();
        exit;
    }

    public function optimizeAction()
    {
        ini_set('memory_limit', '512M');
        if ($this->_getParam('debug')) echo "\noptimize index...\n";
        Kwf_Util_Fulltext::getInstance()->optimize();
        if ($this->_getParam('debug')) echo "done.\n";
        exit;
    }

    public function checkForInvalidAction()
    {
        $this->_checkForInvalid();

        $cmd = "php bootstrap.php fulltext optimize";
        if ($this->_getParam('debug')) $cmd .= " --debug";
        system($cmd);

        exit;
    }

    private function _checkForInvalid()
    {
        $index = Kwf_Util_Fulltext::getInstance();
        if ($this->_getParam('debug')) echo "numDocs: ".$index->numDocs()."\n";
        $query = Zend_Search_Lucene_Search_QueryParser::parse('dummy:dummy');
        $progress = null;
        $documents = $index->find($query);
        if ($this->_getParam('debug')) {
            echo "checking: ".count($documents)."\n";
            $c = new Zend_ProgressBar_Adapter_Console();
            $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_ETA));
            $progress = new Zend_ProgressBar($c, 0, count($documents));
        }
        $i = 0;
        foreach ($documents as $doc) {
            echo ".";
            if ($progress) $progress->next();
            if (!Kwf_Component_Data_Root::getInstance()->getComponentById($doc->componentId)) {
                if ($this->_getParam('debug')) {
                    echo "\n$doc->componentId ist im index aber nicht im Seitenbaum, wird gelöscht...\n";
                }
                $index->delete($doc->id);
                $m = Kwf_Model_Abstract::getInstance('Kwc_FulltextSearch_MetaModel');
                $row = $m->getRow($doc->componentId);
                if ($row) {
                    $row->delete();
                }
            }
            if ($i++ % 10) {
                Kwf_Component_Data_Root::getInstance()->freeMemory();
            }
        }
        if ($progress) $progress->finish();
    }

    public function testAction()
    {
        echo ".";
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById('root-at');
        $childPages = $page->getChildPseudoPages(array(), array('pseudoPage'=>false));
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

    public function rebuildAction()
    {
        ini_set('memory_limit', '512M');
        if (!$this->_getParam('skip-check-for-invalid')) {
            $cmd = "php bootstrap.php fulltext check-for-invalid";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }


        $pageClassesThatCanHaveFulltext = array();
        foreach (self::_getAllPossiblePageComponentClasses() as $c) {
            if (self::_canHaveFulltext($c)) {
                $pageClassesThatCanHaveFulltext[] = $c;
            }
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
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new Kwf_Exception("fork failed");
            } else if ($pid) {
                //parent process
                pcntl_wait($status); //Schützt uns vor Zombie Kindern
                if ($status != 0) {
                    throw new Kwf_Exception("child process failed");
                }

                if ($this->_getParam('debug')) echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
                if (!file_get_contents($queueFile)) {
                    if ($this->_getParam('debug')) echo "fertig.\n";
                    break;
                }
            } else {

                $stats = unserialize(file_get_contents($statsFile));
                while (true) {
                    //child process

                    //echo "memory_usage (child): ".(memory_get_usage()/(1024*1024))."MB\n";
                    if (memory_get_usage() > 64*1024*1024) {
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
                    if ($this->_getParam('debug')) echo " :: $page->url\n";
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
                    if (Kwc_Abstract::getFlag($page->componentClass, 'skipFulltext')) continue;

                    //echo "checking for childComponents\n";
                    $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext', 'inherit' => false));
                    if (Kwc_Abstract::getFlag($page->componentClass, 'hasFulltext')) {
                        $fulltextComponents[] = $page;
                    }
                    if ($fulltextComponents) {
                        if ($this->_getParam('debug')) echo " *** indexing $page->componentId $page->url...";
                        $index = Kwf_Util_Fulltext::getInstance();

                        $doc = new Zend_Search_Lucene_Document();

                        //whole content, for preview in search result
                        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('content', '', 'utf-8'));

                        //normal content with boost=1 goes here
                        $doc->addField(Zend_Search_Lucene_Field::UnStored('normalContent', '', 'utf-8'));

                        $t = $page->getTitle();
                        if (substr($t, -3) == ' - ') $t = substr($t, 0, -3);
                        $field = Zend_Search_Lucene_Field::Text('title', $t, 'utf-8');
                        $field->boost = 10;
                        $doc->addField($field);

                        foreach ($fulltextComponents as $c) {
                            if (method_exists($c->getComponent(), 'modifyFulltextDocument')) {
                                $doc = $c->getComponent()->modifyFulltextDocument($doc);
                            }
                            //Komponente kann null zurückgeben um zu sagen dass gar nicht indiziert werden soll
                            if (!$doc) {
                                if ($this->_getParam('debug')) echo " [no $c->componentId $c->componentClass]";
                                break;
                            }
                            unset($c);
                        }
                        unset($fulltextComponents);
                        if (!$doc->getField('content')->value) {
                            if ($this->_getParam('debug')) echo " [no content]";
                            $doc = null;
                        }
                        if ($this->_getParam('debug')) echo "\n";

                        if ($doc) {
                            //das wird verwendet um alle dokumente im index zu finden
                            //ned wirklisch a schöne lösung :(
                            $field = Zend_Search_Lucene_Field::UnStored('dummy', 'dummy', 'utf-8');
                            $field->boost = 0.0001;
                            $doc->addField($field);

                            $field = Zend_Search_Lucene_Field::Keyword('componentId', $page->componentId, 'utf-8');
                            $field->boost = 0.0001;
                            $doc->addField($field);

                            $subRoot = $page;
                            while ($subRoot) {
                                if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                                $subRoot = $subRoot->parent;
                            }
                            if ($subRoot) {
                                //echo "subroot $subRoot->componentId\n";
                                $field = Zend_Search_Lucene_Field::Keyword('subroot', $subRoot->componentId, 'utf-8');
                                $field->boost = 0.0001;
                                $doc->addField($field);
                            }
                            unset($subRoot);
                            if ($this->_getParam('verbose')) {
                                foreach ($doc->getFieldNames() as $fieldName) {
                                    echo "$fieldName: ".substr($doc->$fieldName, 0, 80)."\n";
                                    //echo "$fieldName: ".$doc->$fieldName."\n";
                                }
                            }

                            $term = new Zend_Search_Lucene_Index_Term($page->componentId, 'componentId');
                            $hits = $index->termDocs($term);
                            foreach ($hits as $id) {
                                //echo "deleting $hit->componentId\n";
                                $index->delete($id);
                            }

                            $index->addDocument($doc);

                            $m = Kwf_Model_Abstract::getInstance('Kwc_FulltextSearch_MetaModel');
                            $row = $m->getRow($page->componentId);
                            if (!$row) {
                                $row = $m->createRow();
                                $row->page_id = $page->componentId;
                            }
                            $row->indexed_date = date('Y-m-d H:i:s');
                            $row->save();
                            unset($row);
                            $stats['indexedPages']++;
                        }
                    }

                }
                file_put_contents($statsFile, serialize($stats));
                if ($this->_getParam('debug')) echo "child finished\n";
                exit(0);
            }
        }


        if ($this->_getParam('debug')) {
            $stats = unserialize(file_get_contents($statsFile));
            echo "fulltext reindex finished.\n";
            echo "duration: ".Kwf_View_Helper_SecondsAsDuration::secondsAsDuration(microtime(true)-$startTime)."s\n";
            echo "used child processes: $numProcesses\n";
            echo "processed pages: $stats[pages]\n";
            echo "indexed pages: $stats[indexedPages]\n";
        }

        if (!$this->_getParam('skip-optimize')) {
            $cmd = "php bootstrap.php fulltext optimize";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            system($cmd);
        }
        exit;
    }

    public function searchAction()
    {
        $index = Kwf_Util_Fulltext::getInstance();

        echo "indexSize ".$index->count()."\n";
        echo "numDocs ".$index->numDocs()."\n";

        $start = microtime(true);

        $queryStr = $this->_getParam('query');
        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);

        $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $query->addSubquery($userQuery, true /* required */);

        if ($this->_getParam('subroot')) {
            $pathTerm  = new Zend_Search_Lucene_Index_Term($this->_getParam('subroot'), 'subroot');
            $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
            $query->addSubquery($pathQuery, true /* required */);
        }
        if ($this->_getParam('news')) {
            $pathTerm  = new Zend_Search_Lucene_Index_Term('kwcNews', 'kwcNews');
            $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
            $query->addSubquery($pathQuery, true /* required */);
        }


        $hits = $index->find($query);
        echo "searched in ".(microtime(true)-$start)."s\n";

        foreach ($hits as $hit) {
            echo "score ".$hit->score."\n";
            echo "  componentId: ".$hit->componentId."\n";
            echo "\n";
        }
        exit;
    }
}
