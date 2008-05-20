<?php
abstract class Vpc_TreeCache_Table extends Vpc_TreeCache_Abstract
{
    protected $_componentClass; //unterkomponenten-klasse
    protected $_childClassKey;  //oder: childComponentClasses-key

    protected $_loadTableFromComponent = true;

    protected $_dbIdShortcut = false;
    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn= 'id';

    protected $_joinTreeCache = true; //wird in Vpc_Root_TreeCache deaktiviert
    protected $_joinTreeCacheOnComponentId = true; //zB Vpc_News_Month_Directory_TreeCache

    protected function _init()
    {
        parent::_init();
        if (!isset($this->_componentClass)) {
            if (isset($this->_childClassKey)) {
                $this->_componentClass = $this->_getChildComponentClass($this->_childClassKey);
            } else {
                throw new Vps_Exception("Entweder _componentClass oder _childClassKey muss gesetzt sein");
            }
        }
    }

    protected function _buildSelect()
    {
        $info = $this->_table->info();

        $select = $this->_cache->getAdapter()->select();
        $select->from(array('t'=>$info['name']), array());
        foreach ($this->_getWhere() as $k=>$i) {
            if (is_int($k)) {
                $select->where($i);
            } else {
                $select->where($k, $i);
            }
        }
        $fields = $this->_getSelectFields();
        $select->from(null, $fields);
        if ($this->_joinTreeCache) {
            if (in_array('component_id', $info['cols']) && $this->_joinTreeCacheOnComponentId) {
                $select->joinLeft(array('tc' => 'vps_tree_cache'), 't.component_id=tc.db_id', array());
            } else {
                $select->joinLeft(array('tc' => 'vps_tree_cache'), '1=1', array());
            }
            $select->where('tc.component_class = ?', $this->_class);
        }
        return $select;
    }

    protected function _insertSelect($select, $mode = 'INSERT')
    {
        $fields = array();
        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $f) {
            $fields[] = $f[2];
        }
        $this->_db->query("$mode INTO vps_tree_cache
                (".implode(', ', $fields).") ($select)");
    }

    public function createMissingChilds()
    {
        $logger = false;
        if (Zend_Registry::isRegistered('debugLogger')) {
            $logger = Zend_Registry::get('debugLogger');
        }
        $select = $this->_buildSelect();
        if ($this->_joinTreeCache) {
            $select->where('tc.generated = ?', Vps_Dao_TreeCache::GENERATE_START);
        }

        if ($logger) {
            $logger->info("table: ".get_class($this));
            $logger->debug($select->__toString());;
            $start = microtime(true);
        }
        $this->_insertSelect($select);

        if ($logger) {
            $time = round(microtime(true)-$start, 2);

            $select->reset(Zend_Db_Select::COLUMNS);
            $select->from(null, array('count' => new Zend_Db_Expr('COUNT(*)')));
            $count = $select->query()->fetchAll();
            $count = $count[0]['count'];
            $logger->debug("Einträge: $count");

            $logger->debug("Dauer: $time sec");
        }

        parent::createMissingChilds();
    }


    protected function _getSelectFields()
    {
        $info = $this->_table->info();

        $fields = array();

        $sql = "CONCAT(tc.component_id, '$this->_idSeparator', $this->_idColumn)";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        if ($this->_dbIdShortcut) {
            $sc = $this->_cache->getAdapter()->quote($this->_dbIdShortcut);
            $sql = "CONCAT($sc, $this->_idColumn)";
        } else {
            $sql = "CONCAT(tc.db_id, '$this->_idSeparator', $this->_idColumn)";
        }
        $fields['db_id'] = new Zend_Db_Expr($sql);

        $fields['parent_component_id'] = 'tc.component_id';

        if ($this->_componentClass == 'row') {
            $fields['component_class'] = 't.component_class';
        } else if (in_array($this->_componentClass, $info['cols'])) {
            $fields['component_class'] = 't.'.$this->_componentClass;
        } else {
            $c = $this->_cache->getAdapter()->quote($this->_componentClass);
            $fields['component_class'] = new Zend_Db_Expr($c);
        }
        if (in_array('pos', $info['cols'])) {
            $fields['pos'] = 't.pos';
        } else {
            $fields['pos'] = new Zend_Db_Expr("0");
        }
        if (in_array('visible', $info['cols'])) {
            $fields['visible'] = 't.visible';
        } else {
            $fields['visible'] = new Zend_Db_Expr("1");
        }

        if ($this->_idColumn instanceof Zend_Db_Expr || in_array('id', $info['cols'])) {
            $fields['tag'] = $this->_idColumn;
        }
        if (!in_array('id', $info['cols']) && !in_array('component_id', $info['cols'])) {
            throw new Vps_Exception("TreeCache_Table currently supports only id or component_id as primary key");
            //vorallem bei onInsertRow usw funktioniert nur das...
            //TODO: andere PrimaryKeys ermöglichen
        }

        $fields['parent_url'] = 'tc.tree_url';
        $fields['tree_url'] = 'tc.tree_url';
        $fields['tree_url_pattern'] = 'tc.tree_url_pattern';

        $fields['parent_component_class'] =
            new Zend_Db_Expr($this->_cache->getAdapter()->quote($this->_class));

        return $fields;
    }

    protected function _getWhere()
    {
        return array();
    }
    public function onInsertRow(Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->getTable() instanceof $this->_table) {
            $select = $this->_buildSelect();
            if (isset($row->id)) {
                $select->where('t.id = ?', $row->id);
            } elseif (isset($row->component_id)) {
                $select->where('t.component_id = ?', $row->component_id);
            } else {
                throw new Vps_Exception("Can't update TreeCache, there is no id or component_id field in table");
            }
            foreach ($select->query()->fetchAll() as $data) {
                $tcRow = $this->_cache->createRow($data);
                $tcRow->generated = Vps_Dao_TreeCache::NOT_GENERATED;
                $this->_updateUrls($tcRow, $row);
                $tcRow->save();
                $saved = false;
                while ($tcRow = $tcRow->findParentComponent()) {
                    $tc = Vpc_TreeCache_Abstract::getInstance($tcRow->component_class);
                    if ($tc instanceof Vpc_TreeCache_AfterGenerate_Interface) {
                        if ($tcRow->generated != Vps_Dao_TreeCache::GENERATE_AFTER) {
                            $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER;
                            $tcRow->save();
                            $saved = true;
                        }
                    }
                }
                if ($saved) {
                    $this->_cache->afterGenerate();
                }
            }
            $this->_cache->clearCache();
        }
        parent::onInsertRow($row);
    }

    //für Vpc_Root_TreeCache
    protected function _updateUrls($tcRow, Vps_Db_Table_Row_Abstract $row)
    {
    }

    public function onDeleteRow(Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->getTable() instanceof $this->_table) {
            //admin erledigt den rest
            $db = $this->_table->getAdapter();
            $where = array();
            $i = $this->_table->info();
            $primaryKey = $i['primary'][1]; //TODO: mehrere primary keys
            $where[] = $db->quoteInto('tag = ?', $row->$primaryKey);
            $where[] = $db->quoteInto('parent_component_class = ?', $this->_class);
            foreach ($this->_cache->fetchAll($where) as $tcRow) {
                $tcRow->delete();
            }
            $this->_cache->clearCache();
        }
        parent::onDeleteRow($row);
    }

    public function onUpdateRow(Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->getTable() instanceof $this->_table) {
            $select = $this->_buildSelect();
            $i = $this->_table->info();
            $primaryKey = $i['primary'][1]; //TODO: mehrere primary keys
            $select->where("t.$primaryKey = ?", $row->$primaryKey);
            $rows = $select->query()->fetchAll();

            $saved = false;
            foreach ($rows as $data) {
                $tcRow = $this->_cache->find($data['component_id'])->current();
                if ($tcRow->component_class != $data['component_class']) {
                    $this->onDeleteRow($row);
                    $this->onInsertRow($row);
                    $this->_cache->afterGenerate();
                    //return, weil falls es mehrere tc-rows für eine row gibt werden
                    //die alle korrekt in onDelete/onInsert gelöscht/erstellt
                    return;
                } else {
                    foreach ($data as $k=>$i) {
                        $tcRow->$k = $i;
                    }
                    $this->_updateUrls($tcRow, $row);
                    $tcRow->save();

                    while ($tcRow = $tcRow->findParentComponent()) {
                        $tc = Vpc_TreeCache_Abstract::getInstance($tcRow->component_class);
                        if ($tc instanceof Vpc_TreeCache_AfterGenerate_Interface) {
                            if ($tcRow->generated != Vps_Dao_TreeCache::GENERATE_AFTER) {
                                $tcRow->generated = Vps_Dao_TreeCache::GENERATE_AFTER;
                                $tcRow->save();
                                $saved = true;
                            }
                        }
                    }
                }
            }
            if ($saved) {
                $this->_cache->afterGenerate();
            }
            $this->_cache->clearCache();
        }
        parent::onUpdateRow($row);
    }
}
