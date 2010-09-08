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
        $MSGKey = 1234;
        $seg = msg_get_queue($MSGKey);
        msg_remove_queue($seg);
        $seg = msg_get_queue($MSGKey);

        Vpc_Abstract::getComponentClasses(); //lädt component-settings-cache
        $db = Vps_Registry::get('db');
        $db->closeConnection();

        //$componentId = 'root';
        $componentId = 'root-verband-master';
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

                //echo "memory_usage: ".(memory_get_usage()/(1024*1024))."MB\n";
                $stat = msg_stat_queue($seg);
                echo "queued: ".$stat['msg_qnum']."\n";
                if ($stat['msg_qnum'] > 0) {
                    msg_receive($seg, 0, $msgtype, 1024, $data);
                    $componentId = $data;
                } else {
                    //echo "fertig.\n";
                    msg_remove_queue($seg);
                    exit;
                }
            } else {
                //child process
                //echo $componentId."\n";
                $page = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
                foreach ($page->getChildPseudoPages(array()) as $c) {
                    msg_send($seg, 1, $c->componentId);
                }

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

                        $field = Zend_Search_Lucene_Field::Keyword('componentId', $page->componentId, 'utf-8');
                        $field->boost = 0.0001;
                        $doc->addField($field);

                        $query = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($page->componentId, 'componentId'));
                        $hits = $index->find($query);
                        foreach ($hits as $hit) {
                            echo "deleting $hit->componentId\n";
                            $index->delete($hit->id);
                        }

                        $index->addDocument($doc);
                    }
                }
                exit(0);
            }
        }
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
