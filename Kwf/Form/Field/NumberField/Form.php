<?php
class Kwf_Form_Field_NumberField_Form extends Kwf_Form_Field_TextField_Form
{
    protected function _init()
    {
        parent::_init();
        unset($this->fields['v_type']);
        $this->add(new Kwf_Form_Field_NumberField('max_value', trlKwf('Maximum Value')))
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_NumberField('min_value', trlKwf('Minimum Value')))
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_Checkbox('allow_negative', trlKwf('Allow Negative')));
        $this->add(new Kwf_Form_Field_Checkbox('allow_decimals', trlKwf('Allow Decimals')));
    }
}
