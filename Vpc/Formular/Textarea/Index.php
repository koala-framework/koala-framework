<?php
class Vpc_Formular_Textarea_Index extends Vpc_Formular_Field_Simple_Abstract
{
    protected $_defaultSettings = array('cols' => '20', 'rows' => '5', 'name' => '', 'value' => '');

    function getTemplateVars()
    {
        $return['cols'] = $this->getSetting('cols');
        $return['rows'] = $this->getSetting('rows');
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/Textarea.html';
        return $return;
    }
}