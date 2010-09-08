<?php
class Vps_Controller_Action_Cli_Web_FulltextController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "various fulltext index commands";
    }

    public function optimizeAction()
    {
        Vps_Util_Fulltext::getInstance()->optimize();
        exit;
    }

    public function rebuildAction()
    {
        $queueFile = 'application/temp/fulltextRebuildQueue';

        Vpc_Abstract::getComponentClasses(); //lädt component-settings-cache
        $db = Vps_Registry::get('db');
        $db->closeConnection();

        $componentId = 'root';
        file_put_contents($queueFile, $componentId);
        while(true) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new Vps_Exception("fork failed");
            } else if ($pid) {
                //parent process
                pcntl_wait($status); //Schützt uns vor Zombie Kindern
                if ($status != 0) {
                    throw new Vps_Exception("child process failed");
                }

                echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
                if (!file_get_contents($queueFile)) {
                    echo "fertig.\n";
                    exit;
                }
            } else {
                while (true) {
                    //child process

                    //echo "memory_usage (child): ".(memory_get_usage()/(1024*1024))."MB\n";
                    if (memory_get_usage() > 50*1024*1024) break;

                    $queue = file_get_contents($queueFile);
                    if (!$queue) break;

                    $queue = explode("\n", $queue);
                    echo "queued: ".count($queue)."\n";
                    $componentId = array_shift($queue);
                    file_put_contents($queueFile, implode("\n", $queue));

                    $page = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
                    echo "==> ".$componentId." $page->url\n";
                    foreach ($page->getChildPseudoPages(array()) as $c) {
                        //echo "queued $c->componentId\n";
                        $queue[] = $c->componentId;
                        file_put_contents($queueFile, implode("\n", $queue));
                    }

                    if (!$page->isPage) continue;

                    //echo "checking for childComponents\n";
                    $fulltextComponents = $page->getRecursiveChildComponents(array('flag'=>'hasFulltext'));
                    if ($fulltextComponents) {
                        echo "indexing $page->componentId $page->url...\n";
                        $index = Vps_Util_Fulltext::getInstance();

                        $doc = new Zend_Search_Lucene_Document();

                        //boost, keywords und disable:
                        //können wenns benötigt werden über eigene komponente die als box eingefügt wird implementiert werden

                        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('content', '', 'utf-8'));

                        $t = $page->getTitle();
                        if (substr($t, -3) == ' - ') $t = substr($t, 0, -3);
                        $field = Zend_Search_Lucene_Field::Text('title', $t, 'utf-8');
                        $field->boost = 10;
                        $doc->addField($field);

                        foreach ($fulltextComponents as $c) {
                            $doc = $c->getComponent()->modifyFulltextDocument($doc);
                            //Komponente kann null zurückgeben um zu sagen dass gar nicht indiziert werden soll
                            if (!$doc) break;
                        }

                        if ($doc) {
                            //das wird verwendet um alle dokumente im index zu finden
                            //ned wirklisch a schöne lösung :(
                            $field = Zend_Search_Lucene_Field::UnStored('dummy', 'dummy', 'utf-8');
                            $doc->addField($field);

                            $field = Zend_Search_Lucene_Field::Keyword('componentId', $page->dbId, 'utf-8');
                            $field->boost = 0.0001;
                            $doc->addField($field);

                            $query = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($page->dbId, 'componentId'));
                            $hits = $index->find($query);
                            foreach ($hits as $hit) {
                                echo "deleting $hit->componentId\n";
                                $index->delete($hit->id);
                            }

                            $index->addDocument($doc);

                            $m = Vps_Model_Abstract::getInstance('Vpc_FulltextSearch_MetaModel');
                            $row = $m->getRow($page->dbId);
                            if (!$row) {
                                $row = $m->createRow();
                                $row->page_id = $page->dbId;
                            }
                            $row->indexed_date = date('Y-m-d H:i:s');
                            $row->save();
                        }
                    }
                }
                //echo "child finished\n";
                exit(0);
            }
        }
        Vps_Util_Fulltext::getInstance()->optimize();
        exit;
    }

    public function searchAction()
    {
        $index = Vps_Util_Fulltext::getInstance();

        echo "indexSize ".$index->count()."\n";
        echo "numDocs ".$index->numDocs()."\n";

        $start = microtime(true);

        $queryStr = $this->_getParam('query');
        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
        $hits = $index->find($query);
        echo "searched in ".(microtime(true)-$start)."s\n";

        foreach ($hits as $hit) {
            echo "score ".$hit->score."\n";
            echo "".$hit->componentId."\n";
            echo "\n";
        }
        exit;
    }
}
