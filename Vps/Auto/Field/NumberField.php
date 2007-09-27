<?php
class Vps_Auto_Field_NumberField extends Vps_Auto_Field_SimpleAbstract
{
    protected $_xtype = 'numberfield';

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getMaxValue()) {
            $this->addValidator(new Zend_Validate_LessThan($this->getMaxValue()));
        }
        if ($this->getMinValue()) {
            $this->addValidator(new Zend_Validate_GreaterThan($this->getMinValue()));
        }
        if ($this->getAllowNegative() === false) {
            $this->addValidator(new Zend_Validate_GreaterThan(0));
        }
        if ($this->getAllowDecimals() === false) {
            $this->addValidator(new Zend_Validate_Digits());
        } else {
            $this->addValidator(new Zend_Validate_Float());
        }
    }
}
