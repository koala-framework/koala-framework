<?php
class Vpc_Formular_Textarea_Component extends Vpc_Formular_Textbox_Component
{
    protected $_settings = array(
        'cols' => '20',
        'rows' => '5',
        'name' => '',
        'value' => ''
    );
    protected $_tablename = 'Vpc_Formular_Textarea_Model';
    const NAME = 'Formular.Textarea';

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['cols'] = $this->getSetting('cols');
        $return['rows'] = $this->getSetting('rows');
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['template'] = 'Formular/Textarea.html';
        return $return;
    }

}