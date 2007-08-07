<?php
class Vpc_Formular_Password_Index extends Vpc_Formular_Field_Simple_Abstract
{
    protected $_settings = array('maxlength' => '255',
								 'name' => '',
								 'width' => '50');

	protected $_tablename = 'Vpc_Formular_Password_IndexModel';
    public $controllerClass = 'Vpc_Formular_Password_IndexController';
    const NAME = 'Formular.Password';


    function getTemplateVars()
    {
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['name'] = $this->getSetting('name');
        $return['width'] = $this->getSetting('width');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/Password.html';
        return $return;
    }
}