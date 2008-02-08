<?php
class Vpc_Formular_Checkbox_Component extends Vpc_Formular_Field_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Checkbox',
            'tablename' => 'Vpc_Formular_Checkbox_Model',
            'default' => array(
                'text' => '',
                'checked' => false,
                'width' => 250,
                'value' => '',
                'validator' => ''
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['value'] = $this->_getRow()->value;
        $return['checked'] = $this->_getRow()->checked;
        $return['text'] = $this->_getRow()->text;
        $return['width'] = $this->_getRow()->width;
        return $return;
    }

    public function processInput()
    {
        $value = isset($_POST[$this->_getName()]) ? '1' : '' ;
        $this->_getRow()->value = $value;
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
            return 'Feld ' . $this->getStore('fieldLabel') . ' ist ein Pflichtfeld.';
        }
        return '';
    }
}
