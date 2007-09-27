<?php
class Vps_Auto_Field_TextField extends Vps_Auto_Field_SimpleAbstract
{
    protected $_xtype = 'textfield';

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getVType() === 'email') {
            $this->addValidator(new Zend_Validate_EmailAddress());
        } else if ($this->getVType() === 'url') {
            //todo, reuse Zend_Uri::check
        } else if ($this->getVType() === 'alpha') {
            $this->addValidator(new Zend_Validate_Alpha());
        } else if ($this->getVType() === 'alphanum') {
            $this->addValidator(new Zend_Validate_Alnum());
        }
    }
}
