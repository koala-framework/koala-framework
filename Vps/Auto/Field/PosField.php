<?php
class Vps_Auto_Field_PosField extends Vps_Auto_Field_SimpleAbstract
{
    protected $_xtype = 'posfield';

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Zend_Validate_Int());
    }
}
