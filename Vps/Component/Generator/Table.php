<?php
class Vps_Component_Generator_Table extends Vps_Component_Generator_Abstract
{
    protected $_loadTableFromComponent = true;

    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    private $_rows = array();

    public function select($parentData, array $select = array())
    {
        $select = new Vps_Component_Select($select);
        $select->whereGenerator($this->_settings['generator']);
        return $select;
    }
    
    public function joinWithChildGenerator($select, $childGenerator)
    {
        $table = $this->_table->info('name');
        $childTable = $childGenerator->_table->info('name');
        $concat = "{$table}.component_id, '{$this->_idSeparator}', {$table}.id";
        $select->setIntegrityCheck(false);
        $select->join($childTable, "CONCAT($concat)={$childTable}.component_id", array());
        return $select;
    }

    public function joinWithParentGenerator($select, $parentGenerator, $grandParentData = null)
    {
        $table = $this->_table->info('name');
        $parentTable = $parentGenerator->_table->info('name');
        $concat = "{$parentTable}.component_id, '{$parentGenerator->_idSeparator}', {$parentTable}.id";
        $select->setIntegrityCheck(false);
        $select->join($parentTable, "CONCAT($concat)={$table}.component_id", array());
        if ($grandParentData) {
            $where = $parentGenerator->select($grandParentData)->getPart(Zend_Db_Select::WHERE);
            $select->where(implode(' ', $where));
        }
        return $select;
    }

    public function getChildData($parentData, $select = array())
    {
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        $rows = array();
        if ($select) {
            $rows = $this->_getModel()->fetchAll($select);
        }
        foreach ($rows as $row) {
            $ret[] = $this->_createData($parentData, $row, $select);
        }
        return $ret;
    }

    public function countChildData($parentData, $select = array())
    {
        if ($select) {
            return $this->_getModel()->fetchCount($select);
        } else {
            return 0;
        }
    }

    protected function _createData($parentData, $row, $select)
    {
        if (!$parentData) {
            $parentData = $this->_getParentDataByRow($row);
        }
        if (!$parentData) {
            throw new Vps_Exception("Can't find parentData in ".get_class($this));
        }
        return parent::_createData($parentData, $row, $select);
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

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (is_null($select)) return null;
        
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            $separator = substr($id, 0, 1);
            $id = substr($id, 1);
            if ($separator != $this->_idSeparator || !is_numeric($id)) {
                return null;
            }
            $select->whereId($id);
        }
        
        $cols = $this->_getModel()->getColumns();
        if ($parentData && in_array('component_id', $cols)) {
            $select->whereEquals('component_id', $parentData->dbId);
        }
        if (in_array('pos', $cols) && !$select->hasPart(Vps_Component_Select::ORDER)) {
            $select->order("pos");
        }
        static $showInvisible;
        if (is_null($showInvisible)) {
            $showInvisible = Vps_Registry::get('config')->showInvisible;
        }
        if (!$select->getPart(Vps_Component_Select::IGNORE_VISIBLE)
            && in_array('visible', $cols) && !$showInvisible) {
            $select->whereEquals("visible", 1);
        }

        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $selectClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            if (!$selectClasses) return null;
            $childClasses = $this->_settings['component'];
            $keys = array();
            foreach ($selectClasses as $selectClass) {
                $key = array_search($selectClass, $childClasses);
                if ($key) $keys[] = $key;
            }
            if (!$keys) return null;

            if (count($childClasses)==1) {
                if (!in_array(key($childClasses), $keys)) {
                    return null;
                }
            } else {
                $select->whereEquals('component', $keys);
            }
        }
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

        $data = array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'row' => $row,
            'isPage' => false,
            'isPseudoPage' => false
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
