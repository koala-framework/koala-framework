<?php
class Vpc_Formular_Password_Index extends Vpc_Formular_Field_Abstract
{
    protected $_settings = array(
        'maxlength' => '255',
        'name' => '',
        'width' => '50'
    );
    protected $_tablename = 'Vpc_Formular_Password_IndexModel';
    const NAME = 'Formular.Password';

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['name'] = $this->getSetting('name');
        $return['width'] = $this->getSetting('width');
        $return['template'] = 'Formular/Password.html';
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
        if($mandatory && $this->getSetting('value') == ''){
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}