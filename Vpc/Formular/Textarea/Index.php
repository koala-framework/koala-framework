<?php
class Vpc_Formular_Textarea_Index extends Vpc_Formular_Field_Simple_Abstract
{
    protected $_settings = array('cols' => '20',
                 'rows' => '5',
                 'name' => '',
                 'value' => '');

    protected $_tablename = 'Vpc_Formular_Textarea_IndexModel';
    public $controllerClass = 'Vpc_Formular_Textarea_IndexController';
    const NAME = 'Formular.Textarea';

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['cols'] = $this->getSetting('cols');
        $return['rows'] = $this->getSetting('rows');
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/Textarea.html';
        return $return;
    }
}