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

    public function getSent()
    {
        return isset($_POST[$this->_getName()]) ? '1' : '' ;
    }

    public function processInput()
    {
        $value = $this->getSent();
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
                return trlVps('Field {0} does not fit to the required formatting ({1})', array($this->getStore('fieldLabel'), $v));
            }
        }
        if ($mandatory && $value == '') {
            return trlVps('Field {0} is mandatory.', $this->getStore('fieldLabel'));
        }
        return '';
    }
}
