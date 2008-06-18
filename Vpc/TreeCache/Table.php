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

    public function getChildData($parentData, $constraints = array())
    {
        $ret = array();
        $pages = $this->_table->fetchAll($this->_formatConstraints($parentData, $constraints)); // TODO: Nummerierung
        foreach ($pages as $row) {
            $ret[] = new Vps_Component_Data_Page($this->_formatConfig($parentData, $row));
        }
        return $ret;
    }
    
    protected function _formatConstraints($parentData, $constraints) {
        return array();
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
            'parent' => $parentData
        );
        return $data;
    }
}
