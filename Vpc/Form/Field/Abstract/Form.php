<?php
class Vpc_Form_Field_Abstract_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('field_label', trlVps('Label')));
        $this->fields->add(new Vps_Form_Field_Checkbox('required', trlVps('Required')));
        $this->fields->add(new Vps_Form_Field_Checkbox('hide_label', trlVps('Hide Label')));
    }
}
