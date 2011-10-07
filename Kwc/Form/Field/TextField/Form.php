<?php
class Vpc_Form_Field_TextField_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);
        $this->fields->add(new Vps_Form_Field_TextField('default_value', trlVps('Default Value')));
        $this->fields->add(new Vps_Form_Field_Select('vtype', trlVps('Validator')))
            ->setShowNoSelection(true)
            ->setEmptyText(trlVps('none'))
            ->setValues(array(
                'email' => trlVps('E-Mail'),
                'url' => trlVps('Url'),
                'alpha' => trlVps('Alpha'),
                'alphanum' => trlVps('Alphanumeric'),
            ));
    }
}
