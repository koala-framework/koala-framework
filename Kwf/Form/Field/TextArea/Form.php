<?php
class Vps_Form_Field_TextArea_Form extends Vps_Form_Field_TextField_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_NumberField('height', trlVps('Height')))
                    ->setWidth(50);
        unset($this->fields['v_type']);
        unset($this->fields['max_length']);
    }
}
