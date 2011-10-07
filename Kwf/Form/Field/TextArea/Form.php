<?php
class Kwf_Form_Field_TextArea_Form extends Kwf_Form_Field_TextField_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
                    ->setWidth(50);
        unset($this->fields['v_type']);
        unset($this->fields['max_length']);
    }
}
