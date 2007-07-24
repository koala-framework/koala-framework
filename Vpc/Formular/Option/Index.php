<?php
class Vpc_Formular_Option_Index extends Vpc_Formular_Field_Decide_Abstract
{
    protected $_defaultSettings = array('text' => '', 'value' => '', 'name' => '', 'checked' => 0, 'horizontal' => '0');
    protected $_options = array();
    
    public function getTemplateVars($mode)
    {
       	
        if ($this->_options == null) $this->getOptions();
		$return['options'] = $this->_options;
		$return['horizontal'] = $this->getSetting('horizontal');
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/Option.html';
        return $return;
    }
    
   public function getOptions ()
   {
        $table = $this->_getTable('Vpc_Formular_Option_OptionsModel');
        $select = $table->fetchAll(array('component_id = ?'  => $this->getComponentId(),
                                             'page_key = ?'      => $this->getPageKey(),
                                             'component_key = ?' => $this->getComponentKey()));        
        //values werden rausgeschrieben
        foreach ($select as $option) {
            $this->_options[] = array('value' => $option->value, 'text' => $option->text, 'checked' => $option->checked, 'id' => $option->id);
        } 
       
    }
}