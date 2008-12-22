<?php
class Vps_Component_Generator_Table extends Vps_Component_Generator_Abstract
{
    protected $_loadTableFromComponent = true;

    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    protected $_hasNumericIds = true;

    public function select($parentData, array $select = array())
    {
        $select = new Vps_Component_Select($select);
        $select->whereGenerator($this->_settings['generator']);
        return $select;
    }

    public function joinWithChildGenerator($select, $childGenerator)
    {
        $table = $this->_getModel()->getTable()->info('name');
        $childTable = $childGenerator->_getModel()->getTable()->info('name');
        $select->setIntegrityCheck(false);
        $select->join($childTable, "{$table}.cache_child_component_id={$childTable}.component_id", array());
        return $select;
    }

    public function joinWithParentGenerator($select, $parentGenerator, $grandParentData = null)
    {
        $table = $this->_getModel()->getTable()->info('name');
        $parentTable = $parentGenerator->_getModel()->getTable()->info('name');
        $select->setIntegrityCheck(false);
        $select->join($parentTable, "{$parentTable}.cache_child_component_id={$table}.component_id", array());
        if ($grandParentData) {
            $parentSelect = $parentGenerator->select($grandParentData);
            $parentSelect = $parentGenerator->_formatSelect($grandParentData, $parentSelect);
            $where = $parentSelect->getPart(Vps_Component_Select::WHERE_EQUALS);
            if ($where) {
                foreach ($parentSelect->getPart(Vps_Component_Select::WHERE_EQUALS) as $key => $value) {
                    if (!strpos($key, '.')) { $key = $parentTable . '.' . $key; }
                    $select->where("$key=?", $value);
                }
            }
            $where = $parentSelect->getPart(Vps_Component_Select::WHERE);
            if ($where) {
                foreach ($where as $key => $value) {
                    if (!strpos($key, '.')) { $key = $parentTable . '.' . $key; }
                    $select->where($key, $value);
                }
            }
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
        $pData = $parentData;
        foreach ($rows as $row) {
            $parentData = $pData;
            if (!$parentData) {
                $parentData = $this->_getParentDataByRow($row, $select);
            }
            if (!is_array($parentData)) {
                if ($parentData) {
                    $parentDatas = array($parentData);
                } else {
                    $parentDatas = array();
                }
            } else {
                $parentDatas = $parentData;
            }
            foreach ($parentDatas as $parentData) {
                if ($parentData->componentClass != $this->_class) {
                    throw new Vps_Exception("_getParentDataByRow returned a component with a wrong componentClass '{$parentData->componentClass}' instead of '$this->_class'");
                }
                $data = $this->_createData($parentData, $row, $select);
                if ($data) {
                    $ret[] = $data;
                }
            }
        }
        return $ret;
    }

    public function countChildData($parentData, $select = array())
    {
        $select = $this->_formatSelect($parentData, $select);
        if ($select) {
            return $this->_getModel()->fetchCount($select);
        } else {
            return 0;
        }
    }

    protected function _createData($parentData, $row, $select)
    {
        return parent::_createData($parentData, $row, $select);
    }

    protected function _getParentDataByRow($row, $select)
    {
        if (isset($row->component_id) && $row->component_id) {
            $constraints = array('componentClass'=>$this->_class);
            if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
                $constraints['subroot'] = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
            }
            $ret = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id, $constraints);
        } else {
            throw new Vps_Exception("Can't find parentData for row, implement _getParentDataByRow for the '{$this->_class}' Generator");
        }
        return $ret;
    }

    protected function _formatSelectId(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            $separator = substr($id, 0, 1);
            $id = substr($id, 1);
            if ($separator != $this->_idSeparator || ($this->_hasNumericIds && !is_numeric($id))) {
                return null;
            }
            $select->whereId($id);
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (is_null($select)) return null;

        $select = $this->_formatSelectId($select);
        if (is_null($select)) return null;

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
        $componentId = $parentData->componentId . $this->_idSeparator . $this->_getIdFromRow($row);
        $dbId = $this->_getIdFromRow($row);
        if (isset($this->_settings['dbIdShortcut'])) {
            $dbId = $this->_settings['dbIdShortcut'] . $dbId;
        } else {
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
