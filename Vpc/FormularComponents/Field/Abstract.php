<?php
abstract class Vpc_Formular_Field_Abstract extends Vpc_Abstract implements Vpc_Formular_Field_Interface
{
    public function validateField($mandatory)
    {
    }

    public function processInput()
    {
    }

    public function getValue()
    {
        $row = $this->_getRow();
        if ($row && isset($row->value)) {
            return $row->value;
        }
        return '';
    }

    protected function _getName()
    {
        return $this->getStore('name');
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['name'] = $this->_getName();
        return $return;
    }
}
