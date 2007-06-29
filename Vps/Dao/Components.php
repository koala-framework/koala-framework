<?php
class Vps_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'vps_components';
    
    public function addComponent($addingComponentId = 0, $class = 'Vpc_Paragraphs_Index', $visible = false)
    {
        // TODO: Componentclass checken
        $data['component'] = $class;
        $data['visible'] = $visible ? 1 : 0;
        $componentId = $this->insert($data);
        if ($addingComponentId == 0) {
            $pageId = $componentId;
        } else {
            $pageId = $this->find($addingComponentId)->current()->top_id;
        }
        $row = $this->find($componentId);
        $row->pageId = $pageId;
        $row->save();
        return $componentId;        
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