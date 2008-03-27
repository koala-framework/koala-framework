<?php
class Vps_Auto_Field_PosField extends Vps_Auto_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('posfield');
    }
    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Zend_Validate_Int());
    }
}
