<?php
class Vps_Form_Container_FieldSet_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Label')))
            ->setWidth(200);
    }
}
