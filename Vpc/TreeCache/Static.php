<?php
class Vpc_TreeCache_Static extends Vpc_TreeCache_Abstract
{
    protected $_classes;
    protected $_idSeparator = '-'; //um in StaticPage _ verwenden zu können

    protected function _init()
    {
        parent::_init();
        foreach ($this->_classes as &$class) {
            if (is_string($class)) continue;
            if (!isset($class['childComponentClass']) && isset($class['childClassKey'])) {
                $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
                $class['componentClass'] = $cls[$class['childClassKey']];
            }
        }
    }
    
    public function createMissingChilds($boxComponentClass = null)
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
            if ($boxComponentClass) { // Wenn MasterBox
                $select->where('NOT ISNULL(tc.url_match)'); // Unter jeder Page
                $select->where('tc.component_class = ?', $boxComponentClass); // der übergebenen Komponent anlegen
                
                // Das kommt wieder weg ;-)
                $componentIds = $this->_db->fetchCol($select);
                $this->_db->query("DELETE FROM vps_tree_cache WHERE component_id IN ('" . implode("', '", $componentIds) . "')");
            } else {
                $select->where('tc.component_class = ?', $this->_class);
                $select->where('tc.generated = ?', Vps_Dao_TreeCache::GENERATE_START);
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

        $sql = "CONCAT(tc.component_id, '{$this->_idSeparator}', ";
        $sql .= $this->_cache->getAdapter()->quote($this->_getChildIdByKey($key));
        $sql .= ")";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        $sql = 'CONCAT(';
        if (isset($class['dbIdShortcut'])) {
            if ($class['dbIdShortcut'] instanceof Zend_Db_Expr) {
                $sql .= $class['dbIdShortcut']->__toString();
            } else {
                $sql .= $this->_cache->getAdapter()->quote($class['dbIdShortcut']);
            }
        } else {
            $sql .= "tc.db_id, '{$this->_idSeparator}', ";
            $sql .= $this->_cache->getAdapter()->quote($this->_getChildIdByKey($key));
        }
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
        if (is_array($c) && isset($c['id'])) return $c['id'];
        return $key;
    }
}
