<?php
class Vpc_Formular_HiddenField_Index extends Vpc_Abstract
{
    protected $_defaultSettings = array('name' => '');
    
    public function getTemplateVars($mode)
    {    
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/HiddenField.html';
        return $return;
    }
}