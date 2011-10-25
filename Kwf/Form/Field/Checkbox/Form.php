<?php
class Kwf_Form_Field_Checkbox_Form extends Kwf_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_Checkbox('default_value', trlKwf('Default Value')));
    }
}
