<?php
class Kwf_Form_Field_TextField_Form extends Kwf_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_NumberField('max_length', trlKwf('Maximum Length')))
            ->setWidth(50);
        $this->add(new Kwf_Form_Field_TextField('default_value', trlKwf('Default Value')))
            ->setWidth(150);
        $this->add(new Kwf_Form_Field_Select('v_type', trlKwf('Validator')))
            ->setValues(array(''=> trlKwf('No Validator'),
                              'email' => trlKwf('E-Mail'),
                              'url' => trlKwf('Url'),
                              'alpha' => trlKwf('Alpha'),
                              'alphanum' => trlKwf('Alpha Numeric')));
    }
}
