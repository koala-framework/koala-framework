<?php
abstract class Vpc_TreeCache_Table extends Vpc_TreeCache_Abstract
{
    protected $_componentClass; //unterkomponenten-klasse
    protected $_childClassKey;  //oder: childComponentClasses-key

    protected $_loadTableFromComponent = true;

    protected $_dbIdShortcut = false;
    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn= 'id';

    public function getDbIdShortcut($dbId)
    {
        if ($this->_dbIdShortcut && 
            substr($dbId, 0, strlen($this->_dbIdShortcut)) == $this->_dbIdShortcut
        ) {
            return $this->_dbIdShortcut;
        }
        return null;
    }
    
    public function select($parentData)
    {
        $select = $this->_table->select();
        if (in_array('component_id', $this->_table->info('cols'))) {
            $select->where('component_id = ?', $parentData->dbId);
        }
        return $select;        
    }
    
    public function getChildData($parentData, $constraints = array())
    {
        $ret = parent::getChildData($parentData, $constraints);
        $select = $this->_formatConstraints($parentData, $constraints);
        if ($select) {
            $pages = $this->_table->fetchAll($select); // TODO: Nummerierung
            foreach ($pages as $row) {
                $ret[] = new $this->_pageDataClass($this->_formatConfig($parentData, $row));
            }
        }
        return $ret;
    }
    
    protected function _formatConstraints($parentData, $constraints)
    {
        $where = parent::_formatConstraints($parentData, $constraints);
        if (is_null($where)) return null;
        if (isset($constraints['select'])) {
            $select = $constraints['select'];
            unset($constraints['select']);
        } else {
            $select = $this->select($parentData);
        }
        
        if (isset($constraints['id'])) {
            $select->where($this->_idColumn . ' = ?', $constraints['id']);
            unset($constraints['id']);
        }

        return $select;
    }
    
    protected function _formatConfig($parentData, $row)
    {
        $componentId = $row->{$this->_idColumn};
        if ($this->_idSeparator && !$parentData instanceof Vps_Component_Data_Root) {
            $componentId = $parentData->componentId . $this->_idSeparator . $componentId;
        }
        $dbId = $row->{$this->_idColumn};
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
            'id' => $row->{$this->_idColumn}
        );
        return $data;
    }
}
