<?php
class Vpc_Formular_Submit_Index extends Vpc_Abstract
{
    protected $_settings = array('name' => '',
										'value' => '');

	protected $_tablename = 'Vpc_Formular_Submit_IndexModel';
    public $controllerClass = 'Vpc_Formular_Submit_IndexController';
    const NAME = 'Formular.Submit';

    function getTemplateVars()
    {
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/Submit.html';
        return $return;
    }
}