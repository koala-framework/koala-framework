<?php
class Vpc_Form_Field_Select_Form extends Vpc_Form_Field_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_NumberField('width', trlVps('Width')))
            ->setComment('px')
            ->setWidth(50)
            ->setAllowNegative(false)
            ->setAllowDecimal(false);

        $mf = $this->fields->add(new Vps_Form_Field_MultiFields('Values'));
        $mf->setMinEntries(0);
        $mf->fields->add(new Vps_Form_Field_TextField('value', trlVps('Value')));
    }
}