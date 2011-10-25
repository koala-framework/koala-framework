<?php
class Kwc_Form_Field_TextField_Trl_Form extends Kwc_Form_Field_Abstract_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('default_value', trlKwf('Default Value')));
    }
}