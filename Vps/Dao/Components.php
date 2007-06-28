<?php
class Vps_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'vps_components';
    
    public function addComponent($class = 'Vpc_Paragraphs_Index', $visible = false)
    {
        // TODO: Componentclass checken
        $data['component'] = $class;
        $data['visible'] = $visible ? 1 : 0;
        return $this->insert($data);
    }
    
    public function deleteComponent($componentId)
    {
        return $this->delete($this->getAdapter()->quoteInto('id = ?', $componentId));
    }
    
    public function setVisible($componentId, $visible)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $componentId);
        $update = array('visible' => $visible ? '1' : '0');
        return $this->update($update, $where) == 1;
    }
    
    public function isVisible($componentId)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $componentId);
        if ($row = $this->fetchRow($where)) {
            return $row->visible == 1;
        }
        return false;
    }
}