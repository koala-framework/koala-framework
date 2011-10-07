<?php
class Vps_Form_Field_TextField_Form extends Vps_Form_Field_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setWidth(50);
        $this->add(new Vps_Form_Field_NumberField('max_length', trlVps('Maximum Length')))
            ->setWidth(50);
        $this->add(new Vps_Form_Field_TextField('default_value', trlVps('Default Value')))
            ->setWidth(150);
        $this->add(new Vps_Form_Field_Select('v_type', trlVps('Validator')))
            ->setValues(array(''=> trlVps('No Validator'),
                              'email' => trlVps('E-Mail'),
                              'url' => trlVps('Url'),
                              'alpha' => trlVps('Alpha'),
                              'alphanum' => trlVps('Alpha Numeric')));
    }
}
