<?php
class Vps_Form_Field_DateTimeField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('vps.datetime');
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        //TODO: $this->addValidator(new Zend_Validate_Date());
    }
}
