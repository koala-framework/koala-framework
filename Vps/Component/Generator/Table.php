<?php
abstract class Vps_Component_Generator_Table extends Vps_Component_Generator_Abstract
{
    protected $_componentClass; //unterkomponenten-klasse
    protected $_childClassKey;  //oder: childComponentClasses-key

    protected $_loadTableFromComponent = true;

    protected $_dbIdShortcut = false;
    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    private $_rows = array();

    public function getDbIdShortcut($dbId)
    {
        if ($this->_dbIdShortcut &&
            substr($dbId, 0, strlen($this->_dbIdShortcut)) == $this->_dbIdShortcut
        ) {
            return $this->_dbIdShortcut;
        }
        return parent::getDbIdShortcut($dbId);
    }

    public function select($parentData)
    {
        $select = new Vps_Db_Table_Select_TreeCache($this->_table);
        $select->setTreeCacheClass(get_class($this));
        $select->from($this->_table);
        $cols = $this->_table->info('cols');
        if ($parentData && in_array('component_id', $cols)) {
            $select->where('component_id = ?', $parentData->dbId);
        }
        if (in_array('visible', $cols) && !Vps_Registry::get('config')->showInvisible) {
            $select->where('visible = ?', 1);
        }
        if (in_array('pos', $cols)) {
            $select->order('pos');
        }
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
            $ret[] = $this->_createData($parentData, $row);
        }
        return $ret;
    }

    protected function _createData($parentData, $row)
    {
        if (!$parentData) {
            $parentData = $this->_getParentDataByRow($row);
        }
        if (!$parentData) {
            throw new Vps_Exception("Can't find parentData in ".get_class($this));
        }
        return parent::_createData($parentData, $row);
    }

    protected function _getParentDataByRow($row)
    {
        if (isset($row->component_id)) {
            $ret = Vps_Component_Data_Root::getInstance()
                                        ->getByDbId($row->component_id);
        } else {
            //TODO: funktioniert das so korrekt?
            $ret = Vps_Component_Data_Root::getInstance()
                                ->getComponentByClass($this->_class);
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
        if (isset($constraints['page']) && $constraints['page']) {
            return null;
        }
        if (isset($constraints['filename'])) {
            return null;
        }
        if (isset($constraints['showInMenu'])) {
            return null;
        }
        if (!isset($constraints['select'])) {
            $constraints['select'] = $this->select($parentData);
        }

        return $constraints;
    }

    protected function _getSelect($parentData, $constraints)
    {
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (!$constraints) return null;

        if (isset($constraints['select'])) {
            $select = $constraints['select'];
        } else {
            $select = $this->select($parentData);
        }
        if (!$select) return null;

        if (isset($constraints['componentClass'])) {
            $constraintClasses = $constraints['componentClass'];
            if (!is_array($constraintClasses)) {
                $constraintClasses = array($constraintClasses);
            }
            if (!$constraintClasses) return null;
            $childClasses = $this->_settings['component'];
            if (!is_array($childClasses)) return null;
            $keys = array();
            foreach ($constraintClasses as $constraintClass) {
                $key = array_search($constraintClass, $childClasses);
                if ($key) $keys[] = $key;
            }
            if (!$keys) return null;
            if (isset($this->_childClassKey)) {
                if (!in_array($this->_childClassKey, $keys)) {
                    return null;
                }
            } else {
                $select->where("component IN ('".implode("', '", $keys) ."')");
            }
        }

        if (isset($constraints['id'])) {
                                                    //- bzw. _ abschneiden
            $select->where($this->_idColumn.' = ?', substr($constraints['id'], 1));
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
        if ($this->_dbIdShortcut) {
            $dbId = $this->_dbIdShortcut . $dbId;
        } else if ($this->_idSeparator && !$parentData instanceof Vps_Component_Data_Root) {
            $dbId = $parentData->dbId . $this->_idSeparator . $dbId;
        }

        if (!isset($this->_childClassKey)) {
            if (isset($row->component)) {
                $childClassKey = $row->component;
            } else {
                throw new Vps_Exception("Either '_childClassKey' must be set or field 'component' in table has to exist for " . get_class($this) . ".");
            }
        } else {
            $childClassKey = $this->_childClassKey;
        }
        $componentClass = $this->_getChildComponentClass($childClassKey);
        
        $data = array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'row' => $row,
            'isPage' => false
        );
        return $data;
    }
    
    /**
     * wird in Link-TreeCache überschrieben
     **/
    protected function _getIdFromRow($row)
    {
        return $row->{$this->_idColumn};
    }

}
