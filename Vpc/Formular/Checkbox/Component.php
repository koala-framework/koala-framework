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
                'name' => '',
                'validator' => ''
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['value'] = $this->_row->value;
        $return['checked'] = $this->_row->checked;
        $return['text'] = $this->_row->text;
        $return['width'] = $this->_row->width;
        $return['name'] = $this->_getName();
        return $return;
    }

    protected function _getName()
    {
        if (isset($this->_row->name)) {
            //subotimal
            return $this->_row->name;
        } else {
            return $this->_store['name'];
        }
    }

    public function processInput()
    {
        $name = $this->_getName();
        if (isset($_POST[$name])) {
            $value = '1';
        } else {
            $value = '';
        }
        $this->_row->value = $value;
    }

    public function validateField($mandatory)
    {
        $value = $this->_row->value;
        $validatorString = $this->_row->validator;
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
