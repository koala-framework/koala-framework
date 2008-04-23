<?php
class Vpc_TreeCache_Static extends Vpc_TreeCache_Abstract
{
    protected $_classes;

    public function createMissingChilds($componentClass = null)
    {
        $logger = false;
        if (Zend_Registry::isRegistered('debugLogger')) {
            $logger = Zend_Registry::get('debugLogger');
        }
        foreach ($this->_classes as $key=>$class) {
            
            $fields = $this->_getSelectFields($key);
            
            $select = new Zend_Db_Select($this->_cache->getAdapter());
            $select->from(array('tc' => 'vps_tree_cache'), array());
            $select->from(null, $fields);
            $select->where('tc.generated = ?', Vps_Dao_TreeCache::GENERATE_START);
            if ($componentClass) {
                //wenn von Vpc_Dao_TreeCache aufgerufen ist componentClass gesetzt
                //es ist dann eine DecoratorBox die unter allen Pages angelegt werden soll
                //wenn die fkt von einer anderen Komponente aufgerufen wird ist componentClass
                //nicht gesetzt, da soll dann auch unter nicht-pages was erstellt werden
                $select->where('NOT ISNULL(tc.url_match)');
                $select->where('tc.component_class = ?', $componentClass);
            } else {
                $select->where('tc.component_class = ?', $this->_class);
            }

            if ($logger) {
                $logger->info("table: ".get_class($this));
                $logger->debug($select->__toString());;
                $start = microtime(true);
            }
            $this->_db->query("INSERT INTO vps_tree_cache
                   (".implode(', ', array_keys($fields)).") ($select)");

            if ($logger) {
                $time = round(microtime(true)-$start, 2);

                $select->reset(Zend_Db_Select::COLUMNS);
                $select->from(null, array('count' => new Zend_Db_Expr('COUNT(*)')));
                $count = $select->query()->fetchAll();
                $count = $count[0]['count'];
                $logger->debug("Einträge: $count");

                $logger->debug("Dauer: $time sec");
            }
        }

        parent::createMissingChilds();
    }
    
    protected function _getSelectFields($key)
    {
        $fields = array();

        $class = $this->_classes[$key];

        if (is_string($class)) {
            $class = array('componentClass'=>$class);
        }

        $sql = 'CONCAT(';
        $parts = array('tc.component_id', $this->_getChildIdByKey($key));
        foreach ($parts as $p) {
            if (substr($p, 0, 3) == 'tc.') {
                $sql .= "$p, ";
            } else {
                $sql .= $this->_cache->getAdapter()->quote($p).", ";
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        $sql = 'CONCAT(';
        $parts = array('tc.db_id', $this->_getChildIdByKey($key));
        foreach ($parts as $p) {
            if (substr($p, 0, 3) == 'tc.') {
                $sql .= "$p, ";
            } else {
                $sql .= $this->_cache->getAdapter()->quote($p).", ";
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";
        $fields['db_id'] = new Zend_Db_Expr($sql);

        $fields['parent_component_id'] = 'tc.component_id';
        $c = $this->_cache->getAdapter()->quote($class['componentClass']);
        $fields['component_class'] = new Zend_Db_Expr($c);
        $fields['pos'] = new Zend_Db_Expr($this->_cache->getAdapter()->quote($key));
        $fields['visible'] = new Zend_Db_Expr("1");
        $fields['parent_url'] = 'tree_url';
        $fields['tree_url'] = 'tree_url';
        $fields['tree_url_pattern'] = 'tree_url_pattern';

        $fields['parent_component_class'] =
            new Zend_Db_Expr($this->_cache->getAdapter()->quote($this->_class));
        return $fields;
    }

    protected function _getChildIdByKey($key)
    {
        $c = $this->_classes[$key];
        if (is_array($c) && isset($c['id'])) return '-'.$c['id'];
        return '-'.$key;
    }
}
