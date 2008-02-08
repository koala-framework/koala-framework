<?php
class Vpc_Formular_Textbox_Component extends Vpc_Formular_Field_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Textbox',
            'tablename' => 'Vpc_Formular_Textbox_Model',
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
        $return['value'] = $this->_getRow()->value;
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
        $validatorString = $this->_getRow()->validator;
        if ($validatorString != '' && $value != '') {
            $validator = new $validatorString();
            if (!$validator->isValid($value)) {
                $v = str_replace('Zend_Validate_', '', $validatorString);
                return 'Das Feld ' . $this->getStore('fieldLabel') . ' entspricht nicht der geforderten Formatierung (' . $v . ')';
            }
        }
        if ($mandatory && $value == '') {
            return 'Feld ' . $this->getStore('fieldLabel') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}
