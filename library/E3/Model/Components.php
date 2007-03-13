<?php
class E3_Model_Components extends Zend_Db_Table
{
    protected $_name = 'components';

    public function getComponentClass($componentId)
    {
        $row = $this->find($componentId);
        return $row->component;
    }
}
