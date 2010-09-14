<?php
class Vpc_Form_Container_FieldSet_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
    }
}