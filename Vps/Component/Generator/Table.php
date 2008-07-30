<?php
class Vps_Component_Generator_Table extends Vps_Component_Generator_Abstract
{
    protected $_loadTableFromComponent = true;

    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    private $_rows = array();

    public function select($parentData, array $constraints = array())
    {
        $select = new Vps_Db_Table_Select_Generator($this->_table);
        $select->setGenerator($this->_settings['generator']);
        $select->from($this->_table);
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
            //Ist diese Exception korrekt - stört das irgendwo?
            //die lösung darunter ist mega uneffizient
            throw new Vps_Exception("Can't find parentData for row, implement _getParentDataByRow for the '{$this->_class}' Generator");
            //$ret = Vps_Component_Data_Root::getInstance()
            //  ->getComponentByClass($this->_class);
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
                                                    //- bzw. _ abschneiden
            $select->where($tableName.".".$this->_idColumn.' = ?', substr($constraints['id'], 1));
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
            'isPage' => false
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
