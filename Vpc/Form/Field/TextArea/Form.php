<?php
class Vpc_Form_Field_TextArea_Form extends Vpc_Form_Field_TextField_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['vtype']);
        $this->fields->insertAfter('width', new Vps_Form_Field_NumberField('height', trlVps('Height')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);

        unset($this->fields['default_value']);
        $this->fields->add(new Vps_Form_Field_TextArea('default_value', trlVps('Default Value')));
    }
}
