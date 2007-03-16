<?php
class E3_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'components';

    public function getComponentClass($componentId)
    {
        $row = $this->find($componentId);

        if (is_null($row->component)) {
        	throw new E3_Dao_Exception("Component with id $componentId does not exists in table components.");
        }
        
        return $row->component;
    }
}
