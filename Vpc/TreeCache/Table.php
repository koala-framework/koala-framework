<?php
abstract class Vpc_TreeCache_Table extends Vpc_TreeCache_Abstract
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
        $select = $this->_table->select();
        $select->from($this->_table);
        if (in_array('component_id', $this->_table->info('cols'))) {
            $select->where('component_id = ?', $parentData->dbId);
        }
        return $select;
    }
    
    public function getChildIds($parentData, $constraints = array())
    {
        $ret = parent::getChildIds($parentData, $constraints);
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if ($constraints) {
            if (isset($constraints['id']) && isset($this->_rows[$constraints['id']])) {
                $ret[] = $constraints['id'];
            } else {
                $select = $this->_getSelect($constraints);
                if ($select) {
                    $rows = $this->_table->fetchAll($select); // TODO: Nummerierung
                    foreach ($rows as $row) {
                        $id = $this->_idSeparator . $this->_getIdFromRow($row);
                        $this->_rows[$id] = $row;
                        $ret[] = $id;
                    }
                }
            }
        }
        return $ret;
    }
    
    public function getChildData($parentData, $id)
    {
        if (isset($this->_rows[$id])) {
            return $this->_createData($this->_formatConfig($parentData, $this->_rows[$id]));
        }
        return parent::getChildData($parentData, $id);
    }
    
    protected function _formatConstraints($parentData, $constraints)
    {
        $where = parent::_formatConstraints($parentData, $constraints);
        if (is_null($where)) return null;
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

    protected function _getSelect($constraints)
    {
        if (isset($constraints['select'])) {
            $select = $constraints['select'];
        } else {
            $select = $this->select();
        }

        if (isset($constraints['componentClass'])) {
            $constraintClasses = $constraints['componentClass'];
            if (!is_array($constraintClasses)) {
                $constraintClasses = array($constraintClasses);
            }
            if (!$constraintClasses) return null;
            $childClasses = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
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
            'id' => $this->_getIdFromRow($row),
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
