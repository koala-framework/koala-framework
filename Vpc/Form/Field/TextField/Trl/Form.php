<?php
class Vpc_Form_Field_TextField_Trl_Form extends Vpc_Form_Field_Abstract_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('default_value', trlVps('Default Value')));
        $this->fields->add(new Vps_Form_Field_ShowField('original_default_value', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('default_value');
    }
}