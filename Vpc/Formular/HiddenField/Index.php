<?php
class Vpc_Formular_HiddenField_Index extends Vpc_Abstract
{
    protected $_settings = array('name' => '');

    protected $_tablename = 'Vpc_Formular_HiddenField_IndexModel';
    public $controllerClass = 'Vpc_Formular_HiddenField_IndexController';
    const NAME = 'Formular.Checkbox';

    public function getTemplateVars()
    {
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/HiddenField.html';
        return $return;
    }
}