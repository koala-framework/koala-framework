<?php
class Vpc_Form_Field_TextField_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setAllowNegative(false)
            ->setAllowDecimal(false);
        $this->fields->add(new Vps_Form_Field_TextField('default_value', trlVps('Default Value')));
    }
}
