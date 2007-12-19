<?php
class Vps_Auto_Field_TimeField extends Vps_Auto_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('timefield');
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Vps_Validate_Time());
    }
}
