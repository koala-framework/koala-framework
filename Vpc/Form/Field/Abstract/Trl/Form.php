<?php
class Vpc_Form_Field_Abstract_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('field_label', trlVps('Label')));
        $this->fields->add(new Vps_Form_Field_ShowField('original_field_label', trlVps('Original')))
            ->setData(new Vps_Data_Trl_OriginalComponent())
            ->getData()->setFieldname('field_label');
    }
}