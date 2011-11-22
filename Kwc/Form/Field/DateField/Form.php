<?php
class Kwc_Form_Field_DateField_Form extends Kwc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_DateField('default_value', trlKwf('Default Value')));
    }
}
