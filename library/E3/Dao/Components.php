<?php
class E3_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'components';

    public function getComponentClass($componentId)
    {
        //$row = $this->fetchRow($this->getAdapter()->quoteInto('id = ?', $componentId));
        $rowset = $this->find($componentId);
        
        if ($rowset->count() == 0) {
        	throw new E3_Dao_Exception("Component with id $componentId does not exist in table components.");
        }
        
        return $rowset->current()->component;
    }
}
