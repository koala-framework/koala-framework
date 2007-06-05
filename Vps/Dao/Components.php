<?php
class Vps_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'vps_components';
    
    public function addComponent($class = 'Vpc_Paragraphs_Index', $status = false)
    {
        // TODO: Componentclass checken
        $data['component'] = $class;
        $data['status'] = $status ? 1 : 0;
        return $this->insert($data);
    }
    
    public function deleteComponent($componentId)
    {
        return $this->delete($this->getAdapter()->quoteInto('id = ?', $componentId));
    }
    
    public function setStatus($componentId, $status)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $componentId);
        $update = array('status' => $status ? '1' : '0');
        return $this->update($update, $where) == 1;
    }
    
    public function getStatus($componentId)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $componentId);
        if ($row = $this->fetchRow($where)) {
            return $row->status == 1;
        }
        return false;
    }
}