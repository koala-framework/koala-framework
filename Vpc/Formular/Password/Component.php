<?php
class Vpc_Formular_Password_Component extends Vpc_Formular_Field_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Password',
            'tablename' => 'Vpc_Formular_Password_Model',
            'default' => array(
                'maxlength' => '255',
                'width' => '50'
            )
        ));
    }

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['width'] = $this->getSetting('width');
        $return['template'] = 'Formular/Password.html';
        return $return;
    }

    public function processInput()
    {
        if (isset($_POST[$this->_getName()])) {
            $value = $_POST[$this->_getName()];
        }
        $this->setSetting('value', $value);
    }

    public function validateField($mandatory)
    {
        if ($mandatory && $this->getSetting('value') == '') {
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}
