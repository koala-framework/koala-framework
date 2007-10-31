<?php
class Vpc_Formular_Textbox_Component extends Vpc_Formular_Field_Abstract
{
    protected $_settings = array(
        'maxlength' => '255',
        'width' => '50',
        'name' => '',
        'value' => '',
        'validator' => ''
    );
    protected $_tablename = 'Vpc_Formular_Textbox_Model';
    const NAME = 'Formular.Textbox';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['value'] = $this->getSetting('value');
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['width'] = $this->getSetting('width');
        $return['name'] = $this->getSetting('name');
        $return['template'] = 'Formular/Textbox.html';
        return $return;
    }

    public function processInput()
    {
        $name = $this->getSetting('name');
        if (isset($_POST[$name])){
            $value = $_POST[$name];
        }
        $this->setSetting('value', $value);
    }

    public function validateField($mandatory)
    {
        $value = $this->getSetting('value');
        $validatorString = $this->getSetting('validator');
        if ($validatorString != '' && $value != ''){
            $validator = new $validatorString();
            if (!$validator->isValid($value)) {
                $v = str_replace('Zend_Validate_', '', $validatorString);
                return 'Das Feld ' . $this->getStore('description') . ' entspricht nicht der geforderten Formatierung (' . $v . ')';
            }
        }
        if($mandatory && $value == ''){
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}