<?php
class Vps_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'components';
    
    public function addComponent($class = 'Vpc_Empty')
    {
        // TODO: Componentclass checken
        $data['component'] = $class;
        return $this->insert($data);
    }
    
    public function deleteComponent($componentId)
    {
        return $this->delete($this->getAdapter()->quoteInto('id = ?', $componentId));
    }
}