<?php
class Vpc_Form_Field_Checkbox_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('box_label', trlVps('Box Label')));
        $this->fields->add(new Vps_Form_Field_Checkbox('default_value', trlVps('Default Value')));
    }
}
