<?php
class Vpc_Formular_HiddenField_Index extends Vpc_Abstract
{
    protected $_defaultSettings = array('name' => '');

    public function getTemplateVars()
    {
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/HiddenField.html';
        return $return;
    }
}