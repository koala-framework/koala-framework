<?php
class Kwc_Form_Field_TextField_Form extends Kwc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);
        $this->fields->add(new Kwf_Form_Field_TextField('default_value', trlKwf('Default Value')));
        $this->fields->add(new Kwf_Form_Field_Select('vtype', trlKwf('Validator')))
            ->setShowNoSelection(true)
            ->setEmptyText(trlKwf('none'))
            ->setValues(array(
                'email' => trlKwf('E-Mail'),
                'url' => trlKwf('Url'),
                'alpha' => trlKwf('Alpha'),
                'alphanum' => trlKwf('Alphanumeric'),
            ));
    }
}
