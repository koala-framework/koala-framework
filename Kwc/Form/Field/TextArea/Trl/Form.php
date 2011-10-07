<?php
class Kwc_Form_Field_TextArea_Trl_Form extends Kwc_Form_Field_TextField_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['default_value']);
        $this->fields->add(new Kwf_Form_Field_TextArea('default_value', trlKwf('Default Value')));
    }
}