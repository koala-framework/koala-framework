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
                'name' => '',
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
        $return['name'] = $this->_getName();
        return $return;
    }

    protected function _getName()
    {
        if (isset($this->_getRow()->name)) {
            //subotimal
            return $this->_getRow()->name;
        } else {
            return $this->_store['name'];
        }
    }

    public function processInput()
    {
        $name = $this->_getName();
        if (isset($_POST[$name])) {
            $this->_getRow()->value = $_POST[$name];
        }
    }

    public function validateField($mandatory)
    {
        $value = $this->_getRow()->value;
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
