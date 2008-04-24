<?php
class Vps_Form_Field_Checkbox_Form extends Vps_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_Checkbox('default_value', trlVps('Default Value')));
    }
}
