<?php
class Vps_Component_Generator_Table extends Vps_Component_Generator_Abstract
{
    protected $_loadTableFromComponent = true;

    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    private $_rows = array();

    protected function _getSelectFields()
    {
        return array(Zend_Db_Select::SQL_WILDCARD);
    }

    public function select($parentData, array $constraints = array())
    {
        $select = new Vps_Db_Table_Select_Generator($this->_table);
        $select->setGenerator($this->_settings['generator']);
        $select->from($this->_table, $this->_getSelectFields());
        $cols = $this->_table->info('cols');
        $tableName = $this->_table->info('name');
        if ($parentData && in_array('component_id', $cols)) {
            $select->where("$tableName.component_id = ?", $parentData->dbId);
        }
        if ((!isset($constraints['ignoreVisible']) || !$constraints['ignoreVisible'])
            && in_array('visible', $cols) && !Vps_Registry::get('config')->showInvisible) {
            $select->where("$tableName.visible = ?", 1);
        }
        if (in_array('pos', $cols)) {
            $select->order("$tableName.pos");
        }
        return $select;
    }
    
    public function joinWithChildGenerator($select, $childGenerator, $parentData = null)
    {
        $table = $this->_table->info('name');
        $childTable = $childGenerator->_table->info('name');
        $concat = "{$table}.component_id, '{$this->_idSeparator}', {$table}.id";
        $select->join($table, "CONCAT($concat)={$childTable}.component_id", array());
        if ($parentData) {
            $where = $this->select($parentData)->getPart(Zend_Db_Select::WHERE);
            $select->where(implode(' ', $where));
        }
        return $select;
    }

    public function joinWithParentGenerator($select, $parentGenerator)
    {
        $table = $this->_table->info('name');
        $parentTable = $parentGenerator->_table->info('name');
        $concat = "{$parentTable}.component_id, '{$parentGenerator->_idSeparator}', {$parentTable}.id";
        $select->join($table, "CONCAT($concat)={$table}.component_id", array());
        return $select;
    }

    public function joinedSelect($grandParentGenerator, $grandParentData)
    {
        $table = $this->_table->info('name');
        $select = $this->select(null);
        $select->setIntegrityCheck(false);
        $select = $grandParentGenerator->joinWithChildGenerator($select, $this, $grandParentData);
        return $select;
    }

    public function getChildIds($parentData, $constraints = array())
    {
        $ret = parent::getChildIds($parentData, $constraints);
        if (!$parentData) {
            throw new Vps_Exception("no parentData for getChildIds is not (yet) implemented");
        }
        foreach ($this->_fetchRows($parentData, $constraints) as $row) {
            $ret[] = $this->_idSeparator . $this->_getIdFromRow($row);
        }
        return $ret;
    }
    public function getChildData($parentData, $constraints = array())
    {
        $ret = parent::getChildData($parentData, $constraints);
        foreach ($this->_fetchRows($parentData, $constraints) as $row) {
            $ret[] = $this->_createData($parentData, $row, $constraints);
        }
        return $ret;
    }

    protected function _createData($parentData, $row, $constraints)
    {
        if (!$parentData) {
            $parentData = $this->_getParentDataByRow($row);
        }
        if (!$parentData) {
            throw new Vps_Exception("Can't find parentData in ".get_class($this));
        }
        return parent::_createData($parentData, $row, $constraints);
    }

    protected function _getParentDataByRow($row)
    {
        if (isset($row->component_id)) {
            $ret = Vps_Component_Data_Root::getInstance()
                                        ->getComponentByDbId($row->component_id);
        } else {
            throw new Vps_Exception("Can't find parentData for row, implement _getParentDataByRow for the '{$this->_class}' Generator");
        }
        return $ret;
    }

    protected function _fetchRows($parentData, $constraints)
    {
        $select = $this->_getSelect($parentData, $constraints);
        if ($select) {
            return $this->_table->fetchAll($select);
        }
        return array();
    }

    protected function _formatConstraints($parentData, $constraints)
    {
        $constraints = parent::_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return null;
        if (isset($constraints['filename'])) {
            return null;
        }
        if (isset($constraints['showInMenu'])) {
            return null;
        }
        if (!isset($constraints['select'])) {
            $constraints['select'] = $this->select($parentData, $constraints);
        }
        if (isset($constraints['id'])) {
            if (!is_numeric(substr($constraints['id'], 1))) return null;
        }
        if (isset($constraints['inherit'])) {
            return null;
        }
        
        return $constraints;
    }

    protected function _getSelect($parentData, $constraints)
    {
        $tableName = $this->_table->info('name');

        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (!$constraints) return null;
        $select = $constraints['select'];
        if (!$select) return null;

        if (isset($constraints['componentClass'])) {
            $constraintClasses = $constraints['componentClass'];
            if (!is_array($constraintClasses)) {
                $constraintClasses = array($constraintClasses);
            }
            if (!$constraintClasses) return null;
            $childClasses = $this->_settings['component'];
            $keys = array();
            foreach ($constraintClasses as $constraintClass) {
                $key = array_search($constraintClass, $childClasses);
                if ($key) $keys[] = $key;
            }
            if (!$keys) return null;

            if (count($childClasses)==1) {
                if (!in_array(key($childClasses), $keys)) {
                    return null;
                }
            } else {
                $select->where("$tableName.component IN ('".implode("', '", $keys) ."')");
            }
        }
        if (isset($constraints['id'])) {
            $selectFields = $this->_getSelectFields();
            // mit substr - bzw. _ abschneiden
            if (array_key_exists($this->_idColumn, $selectFields)) {
                $select->where($selectFields[$this->_idColumn].' = ?', substr($constraints['id'], 1));
            } else {
                $select->where($tableName.".".$this->_idColumn.' = ?', substr($constraints['id'], 1));
            }

        }

        if (isset($constraints['limit'])) $select->limit($constraints['limit']);

        return $select;
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentId = $this->_getIdFromRow($row);
        if ($this->_idSeparator && !$parentData instanceof Vps_Component_Data_Root) {
            $componentId = $parentData->componentId . $this->_idSeparator . $componentId;
        }
        $dbId = $this->_getIdFromRow($row);
        if (isset($this->_settings['dbIdShortcut'])) {
            $dbId = $this->_settings['dbIdShortcut'] . $dbId;
        } else if ($this->_idSeparator && !$parentData instanceof Vps_Component_Data_Root) {
            $dbId = $parentData->dbId . $this->_idSeparator . $dbId;
        }

        if (count($this->_settings['component']) > 1) {
            if (isset($row->component)) {
                if (!isset($this->_settings['component'][$row->component])) {
                    throw new Vps_Exception("Component stored in table does is not valid child: '{$row->component}' (for component '$this->_class')");
                }
                $componentClass = $this->_settings['component'][$row->component];
            } else {
                throw new Vps_Exception("Either only one component or field 'component' in table has to exist for " . get_class($this) . " ($this->_class).");
            }
        } else {
            $componentClass = current($this->_settings['component']);
        }
        
        $visible = true;
        if (in_array('visible', $this->_table->info('cols'))) {
            $visible = $row->visible == '1';
        }
                
        $data = array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'row' => $row,
            'isPage' => false,
            'isPseudoPage' => false,
            'visible' => $visible
        );
        return $data;
    }
    
    /**
     * wird in Link-Generator überschrieben
     **/
    protected function _getIdFromRow($row)
    {
        return $row->{$this->_idColumn};
    }

}
