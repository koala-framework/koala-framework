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

    public function getChildIds($parentData, $select = array())
    {
        if (!$this->_getModel() instanceof Vps_Model_Interface_Id) {
            throw new Vps_Exception('Model for getChildIds must implement Vps_Model_Interface_Id');
        }
        $select = $this->_formatSelect($parentData, $select);
        return $this->_getModel()->getIds($select);
    }

    public function getChildData($parentData, $select = array())
    {
        Vps_Benchmark::count('GenTable::getChildData');
        if (is_array($select)) $select = new Vps_Component_Select($select);
        $ret = array();
        if (!$parentData && ($p = $select->getPart(Vps_Component_Select::WHERE_ON_SAME_PAGE))
                && !$this->_getModel()->hasColumn('component_id')) {
            $parentDatas = $p->getRecursiveChildComponents(array(
                'componentClass' => $this->_class
            ));
        } else {
            $parentDatas = array($parentData /* kann auch null sein*/);
        }

        foreach ($parentDatas as $parentData) {
            $select = $this->_formatSelect($parentData, $select);
            $rows = array();
            if ($select) {
                $rows = $this->_getModel()->fetchAll($select);
            }
            foreach ($rows as $row) {
                $currentPd = $parentData;
                if (!$currentPd) {
                    $currentPd = $this->_getParentDataByRow($row, $select);
                }
                if (!is_array($currentPd)) {
                    if ($currentPd) {
                        $currentPds = array($currentPd);
                    } else {
                        $currentPds = array();
                    }
                } else {
                    $currentPds = $currentPd;
                }
                foreach ($currentPds as $currentPd) {
                    if ($currentPd->componentClass != $this->_class) {
                        throw new Vps_Exception("_getParentDataByRow returned a component with a wrong componentClass '{$currentPd->componentClass}' instead of '$this->_class'");
                    }
                    $data = $this->_createData($currentPd, $row, $select);
                    if ($data) {
                        $ret[] = $data;
                    }
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
            if (in_array($separator, array('_', '-'))) {
                $id = substr($id, 1);
                if ($separator != $this->_idSeparator || ($this->_hasNumericIds && !is_numeric($id))) {
                    return null;
                }
                $select->whereId($id);
            }
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        if ($this->_getModel()->hasColumn('component_id')) {
            if ($parentData) {
                $select->whereEquals('component_id', $parentData->dbId);
            } else if ($p = $select->getPart(Vps_Component_Select::WHERE_ON_SAME_PAGE)) {
                $p = $p->getPageOrRoot();
                $select->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_StartsWith('component_id', $p->dbId.'-'),
                    new Vps_Model_Select_Expr_Equals('component_id', $p->dbId),
                )));
            }
        }

        $select = parent::_formatSelect($parentData, $select);
        if (is_null($select)) return null;

        $select = $this->_formatSelectId($select);
        if (is_null($select)) return null;

        if ($this->_getModel()->hasColumn('pos') && !$select->hasPart(Vps_Component_Select::ORDER)) {
            $select->order("pos");
        }

        static $showInvisible;
        if (is_null($showInvisible)) {
            $showInvisible = Vps_Registry::get('config')->showInvisible;
        }
        if (!$select->getPart(Vps_Component_Select::IGNORE_VISIBLE)
            && $this->_getModel()->hasColumn('visible') && !$showInvisible) {
            $select->whereEquals("visible", 1);
        }

        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $selectClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            if (!$selectClasses) return null;
            $childClasses = $this->_settings['component'];
            $keys = array();
            foreach ($selectClasses as $selectClass) {
                $keys = array_merge($keys, array_keys($childClasses, $selectClass));
            }

            if (!$keys) return null;

            if (count($childClasses)==1) {
                if (!in_array(key($childClasses), $keys)) {
                    return null;
                }
            } else {
                if (!$this->_getModel()->hasColumn('component')) {
                    throw new Vps_Exception("Column component does not exist for a generator in '$this->_class'");
                }
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
                if (!isset($this->_settings['component'][$row->component]) || !$this->_settings['component'][$row->component]) {
                    throw new Vps_Exception("Component stored in table does is not valid child: '{$row->component}' (for component '$this->_class')");
                }
                $componentClass = $this->_settings['component'][$row->component];
            } else {
                throw new Vps_Exception("Either only one component or field 'component' in table has to exist for " . get_class($this) . " ($this->_class).");
            }
        } else {
            reset($this->_settings['component']);
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
