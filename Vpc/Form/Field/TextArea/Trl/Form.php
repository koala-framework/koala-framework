<?php
class Vpc_Form_Field_TextArea_Trl_Form extends Vpc_Form_Field_TextField_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['default_value']);
        $this->fields->add(new Vps_Form_Field_TextArea('default_value', trlVps('Default Value')));
    }
}