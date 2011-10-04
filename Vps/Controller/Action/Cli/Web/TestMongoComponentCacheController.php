<?php
class Vps_Controller_Action_Cli_Web_TestMongoComponentCacheController extends Vps_Controller_Action
{
    public function indexAction()
    {
        set_time_limit(0);

        $db = Vps_Registry::get('dao')->getMongoDb();
        $pageSize = 5000;

        echo "erstelle index auf cache_component_meta.value...\n";
//         Vps_Registry::get('db')->query("ALTER TABLE cache_component_meta ADD INDEX(value)");
//         Vps_Registry::get('db')->query("ALTER TABLE `cache_component` ADD `expired` TINYINT NOT NULL");
//         Vps_Registry::get('db')->query("ALTER TABLE `cache_component` ADD INDEX ( `expired` )");
 
        $cnt = Vps_Registry::get('db')->query("SELECT COUNT(*) FROM cache_component")->fetchColumn();
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        echo "konvertiere $cnt cache_component eintraege...\n";
        $progress = new Zend_ProgressBar($c, 0, $cnt);

        $db->component_cache->drop();
        for($offset=0; $offset<$cnt; $offset += $pageSize) {
            $rowsToInsert = array();
            $rows = Vps_Registry::get('db')
                ->query("SELECT * FROM cache_component ORDER BY id LIMIT $pageSize OFFSET $offset")->fetchAll();
            $metaCacheId = array();
            $metaComponentClass = array();
            $ids = array();
            $componentClasses = array();
            foreach ($rows as $row) {
                if (!in_array($row['id'], $ids)) {
                    $ids[] = $row['id'];
                    $metaCacheId[$row['id']] = array();
                }
                if (!in_array($row['component_class'], $componentClasses)) {
                    $componentClasses[] = $row['component_class'];
                    $metaComponentClass[$row['component_class']] = array();
                }
            }
            foreach (Vps_Registry::get('db')
                    ->query("SELECT * FROM cache_component_meta WHERE
                        (value IN ('".implode("', '", $ids)."') AND type='cacheId')
                        OR (value IN ('".implode("', '", $componentClasses)."') AND type='componentClass')
                        OR (value IN ('".implode("', '", $componentClasses)."') AND type='callback')
                    ")->fetchAll() as $metaRow) {
                if ($metaRow['type'] == 'cacheId') {
                    $metaCacheId[$metaRow['value']][] = $metaRow;
                } else {
                    $metaComponentClass[$metaRow['value']][] = $metaRow;
                }
            }
            foreach ($rows as $row) {
                $progress->next();
                $row['last_modified'] = new MongoDate((int)$row['last_modified']);
                if ($row['expire']) {
                    $row['expire'] = new MongoDate((int)$row['expire']);
                } else {
                    unset($row['expire']);
                }
                $row['meta'] = array_merge($metaCacheId[$row['id']], $metaComponentClass[$row['component_class']]);
                //zumüllen
                for($i=0;$i<5;$i++) {
                    $row['meta'][] = array(
                        'model' => 'Vps_Model_Foo_Bar',
                        'id' => (string)rand(1, 10000),
                        'field' => null,
                        'value' => (string)rand(1, 10000),
                        'type' => 'cacheId'
                    );
                }

                $rowsToInsert[] = $row;
            }
            $db->component_cache->batchInsert($rowsToInsert);
        }
        die('fertig');
    }

    public function createIndexAction()
    {
        $db = Vps_Registry::get('dao')->getMongoDb();

        $db->component_cache->ensureIndex(array(
            'meta.type' => 1,
            'meta.model' => 1,
            'meta.id' => 1,
            'meta.field' => 1,
        ));
        exit;
    }

    private function _getTestQuery1()
    {
        $ret = array(
            '$or' => array(
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'cacheId',
                            'model'=> 'Vpc_Paragraphs_Model',
                            'id' => '1',
                            'field' => 'component_id'
                        )
                    )
                ),
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'componentClass',
                            'model'=> 'Vpc_Paragraphs_Model'
                        )
                    )
                ),
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'callback',
                            'model'=> 'Vpc_Paragraphs_Model',
                            'id' => '1',
                            'field' => 'component_id'
                        )
                    )
                )
            )
        );
        return $ret;
    }

    public function readAction()
    {
        $db = Vps_Registry::get('dao')->getMongoDb();

        
        $s = microtime(true);
        $res = array();
        foreach ($db->component_cache->find($this->_getTestQuery1()) as $i) {
            $res[] = $i;
        }
        echo "query finished in ".(microtime(true)-$s)." s\n";
        foreach ($res as $i) {
            echo $i['id'].' '.$i['component_class']."\n";
        }
        

        echo "\nmysql:\n";
        $s = microtime(true);
/*        $res = Vps_Registry::get('db')->query("SELECT * FROM cache_component
            JOIN cache_component_meta ON ((type='cacheId' AND value=cache_component.id)
                                        OR (type='componentClass' AND value=cache_component.component_class)
                                        OR (type='callback' AND value=cache_component.component_class)
                                        )
            WHERE cache_component_meta.model='Vpc_Paragraphs_Model'
            AND ((type='cacheId' AND cache_component_meta.id=1) OR type='componentClass' OR type='callback')
        ")->fetchAll();*/
        $res = Vps_Registry::get('db')->query("SELECT cache_component.* FROM cache_component
            JOIN cache_component_meta ON (type='cacheId' AND value=cache_component.id)
            WHERE cache_component_meta.model='Vpc_Paragraphs_Model'
            AND type='cacheId' AND cache_component_meta.id='1'
            GROUP BY cache_component.id
        ")->fetchAll();
        echo "query finished in ".(microtime(true)-$s)." s\n";
        foreach ($res as $i) {
            echo $i['id'].' '.$i['page_id'].' '.$i['component_class']."\n";
        }
        exit;
    }

    private function _getTestQuery2()
    {
        $ret = array(
            '$or' => array(
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'cacheId',
                            'model'=> 'Vps_Dao_Pages',
                            'id' => '1',
                            'field' => 'id'
                        )
                    )
                ),
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'componentClass',
                            'model'=> 'Vps_Dao_Pages'
                        )
                    )
                ),
                array(
                    'meta' => array(
                        '$elemMatch' => array(
                            'type' => 'callback',
                            'model'=> 'Vps_Dao_Pages',
                            'id' => '1',
                            'field' => 'id'
                        )
                    )
                )
            )
        );
        return $ret;
    }

    public function read2Action()
    {
        $db = Vps_Registry::get('dao')->getMongoDb();


        $s = microtime(true);
        $res = array();
        foreach ($db->component_cache->find($this->_getTestQuery2())->limit(10) as $i) {
            $res[] = $i;
        }
        echo "query finished in ".(microtime(true)-$s)." s\n";
        foreach ($res as $i) {
            echo $i['id'].' '.$i['component_class']."\n";
        }


        echo "\nmysql:\n";
        $s = microtime(true);
/*        $res = Vps_Registry::get('db')->query("SELECT * FROM cache_component
            JOIN cache_component_meta ON ((type='cacheId' AND value=cache_component.id)
                                        OR (type='componentClass' AND value=cache_component.component_class)
                                        OR (type='callback' AND value=cache_component.component_class)
                                        )
            WHERE cache_component_meta.model='Vpc_Paragraphs_Model'
            AND ((type='cacheId' AND cache_component_meta.id=1) OR type='componentClass' OR type='callback')
        ")->fetchAll();*/
        $res = Vps_Registry::get('db')->query("SELECT cache_component.* FROM cache_component
            JOIN cache_component_meta ON (type='componentClass' AND value=cache_component.component_class)
            WHERE cache_component_meta.model='Vps_Dao_Pages'
            AND type='componentClass'
            GROUP BY cache_component.id
            LIMIT 10
        ")->fetchAll();
        echo "query finished in ".(microtime(true)-$s)." s\n";
        foreach ($res as $i) {
            echo $i['id'].' '.$i['page_id'].' '.$i['component_class']."\n";
        }
        exit;
    }

    public function expire2Action()
    {
        $db = Vps_Registry::get('dao')->getMongoDb();

        $s = microtime(true);
        $res = $db->component_cache->update($this->_getTestQuery2(),
            array('$set'=>array('expired'=>true)),
            array('safe'=>true, 'multiple'=>true));
        echo "mongo update finished in ".(microtime(true)-$s)." s\n";
        p($res);

        $s = microtime(true);
        $res = Vps_Registry::get('db')->query("UPDATE cache_component, cache_component_meta
            SET cache_component.expired=1
            WHERE cache_component_meta.model='Vps_Dao_Pages'
            AND cache_component_meta.type='componentClass'
            AND cache_component_meta.value=cache_component.component_class
        ");
        echo "mysql update finished in ".(microtime(true)-$s)." s\n";
        exit;
    }

    public function explainAction()
    {
        $db = Vps_Registry::get('dao')->getMongoDb();

        d($db->component_cache->find($this->_getTestQuery1())->explain());
        exit;
    }
}