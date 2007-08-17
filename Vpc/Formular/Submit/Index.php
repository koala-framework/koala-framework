<?php
class Vpc_Formular_Submit_Index extends Vpc_Abstract
{
    protected $_settings = array(
        'name' => 'submit',
        'text' => 'Senden'
    );
    protected $_tablename = 'Vpc_Formular_Submit_IndexModel';
    public $controllerClass = 'Vpc_Formular_Submit_IndexController';
    const NAME = 'Formular.Submit';

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['name'] = $this->getSetting('name');
        $return['text'] = $this->getSetting('text');
        $return['template'] = 'Formular/Submit.html';
        return $return;
    }
}