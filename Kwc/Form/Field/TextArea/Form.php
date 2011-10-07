<?php
class Kwc_Form_Field_TextArea_Form extends Kwc_Form_Field_TextField_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['vtype']);
        $this->fields->insertAfter('width', new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);

        unset($this->fields['default_value']);
        $this->fields->add(new Kwf_Form_Field_TextArea('default_value', trlKwf('Default Value')));
    }
}
