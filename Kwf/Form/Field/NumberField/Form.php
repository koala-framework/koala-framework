<?php
class Vps_Form_Field_NumberField_Form extends Vps_Form_Field_TextField_Form
{
    protected function _init()
    {
        parent::_init();
        unset($this->fields['v_type']);
        $this->add(new Vps_Form_Field_NumberField('max_value', trlVps('Maximum Value')))
            ->setWidth(50);
        $this->add(new Vps_Form_Field_NumberField('min_value', trlVps('Minimum Value')))
            ->setWidth(50);
        $this->add(new Vps_Form_Field_Checkbox('allow_negative', trlVps('Allow Negative')));
        $this->add(new Vps_Form_Field_Checkbox('allow_decimals', trlVps('Allow Decimals')));
    }
}
