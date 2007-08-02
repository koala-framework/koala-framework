<?php
class Vpc_Formular_Textbox_Index extends Vpc_Formular_Field_Simple_Abstract
{
    protected $_defaultSettings = array('maxlength' => '255',  'width' => '50','name' => '', 'value' => '', 'validator' => '');

    public function getTemplateVars()
    {
        $return['value'] = $this->getSetting('value');
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['width'] = $this->getSetting('width');
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/Textbox.html';
        return $return;
    }

    public function setWidth($width)
    {
        $this->_width = (int)$width;
    }

    public function validateField($mandatory)
    {

        $validatorString = $this->getSetting('validator');
        if ($validatorString != '' && $this->getSetting('value') != ''){
            $validator = new $validatorString();
            if (!$validator->isValid($this->getSetting('value'))) return 'Das Feld '.$this->_errorField.' entspricht nicht der geforderten Formattierung';
        }
        return parent::validateField($mandatory);
    }
}