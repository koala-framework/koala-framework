<?php
class Vpc_Formular_Submit_Index extends Vpc_Abstract
{
    protected $_defaultSettings = array('name' => '', 'value' => '');
    
    function getTemplateVars()
    {
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/Submit.html';
        return $return;
    }
}