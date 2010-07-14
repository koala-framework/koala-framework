<?php
class Vpc_Form_Field_TextArea_Form extends Vpc_Form_Field_TextField_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->insertAfter('width', new Vps_Form_Field_NumberField('height', trlVps('Height')))
            ->setAllowNegative(false)
            ->setAllowDecimal(false);
    }
}
