<?php
class Vpc_Formular_Password_Component extends Vpc_Formular_Field_Abstract
                                      implements Vpc_Formular_Field_Interface
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Password',
            'tablename' => 'Vpc_Formular_Password_Model',
            'default' => array(
                'maxlength' => '255',
                'width' => '150',
                'value' => '',
                'validator' => ''
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['maxlength'] = $this->_getRow()->maxlength;
        $return['width'] = $this->_getRow()->width;
        return $return;
    }

    public function processInput()
    {
        if (isset($_POST[$this->_getName()])) {
            $this->_getRow()->value = $_POST[$this->_getName()];
        }
    }

    public function validateField($mandatory)
    {
        $value = $this->getValue();
        if ($mandatory && $value == '') {
            return trlVps('Field {0} is mandatory, please fill out', $this->getStore('fieldLabel'));
        }
        return '';
    }
}
