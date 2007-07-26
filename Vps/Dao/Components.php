<?php
class Vps_Dao_Components extends Zend_Db_Table
{
    protected $_name = 'vps_components';
    
    public function addComponent($addingComponentId = 0, $class = 'Vpc_Paragraphs_Index', $visible = false)
    {
        // Setup
        try {
            $setupClass = str_replace('_Index', '_Setup', $class);
            if (class_exists($setupClass)) {
                $setup = new $setupClass($this->getAdapter());
                $setup->setup();
            }
        } catch (Zend_Exception $e) {
        }

        $components = new Vps_Config_Ini('application/components.ini');
        $config = call_user_func(array($class, 'getStaticSettings')); 
        foreach ($config as $element => $value){
            if (!$components->checkKeyExists($class, $element)) {
                $components->setValue($class, $element, (string)$value);       
            }       
        }     
        $components->write();

        // TODO: Componentclass checken
        $data['component'] = $class;
        $data['visible'] = $visible ? 1 : 0;
        $componentId = $this->insert($data);
        if ($addingComponentId == 0) {
            $pageId = $componentId;
        } else {
            $pageId = $this->find($addingComponentId)->current()->page_id;
        }
        $row = $this->find($componentId)->current();
        $row->page_id = $pageId;
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
    
    public function getAvailableComponents()
    {
        $ini = new Zend_Config_Ini('application/components.ini', 'Vpc_Simple_Textbox_Index', true);
        $ini->test = 'foo';
        p($ini->toArray());
        p($ini->areAllSectionsLoaded ());
    }
    
    
}