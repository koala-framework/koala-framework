<?php
class Vps_Form_Field_Abstract_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('field_label', trlVps('Label')))
            ->setWidth(200);
        $this->add(new Vps_Form_Field_Checkbox('allow_blank', trlVps('Allow Blank')));
    }
}
