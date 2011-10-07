<?php
class Kwc_Advanced_Team_Member_Data_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_ShowField('title', trlKwf('Title')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
        $this->add(new Kwf_Form_Field_ShowField('firstname', trlKwf('First name')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
        $this->add(new Kwf_Form_Field_ShowField('lastname', trlKwf('Last name')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
        $this->add(new Kwf_Form_Field_TextField('working_position', trlKwf('Position')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_ShowField('original_working_position', trlKwf('Original Position')))
            ->setData(new Kwf_Data_Trl_OriginalComponent('working_position'));
        $this->add(new Kwf_Form_Field_ShowField('phone', trlKwf('Phone')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
        $this->add(new Kwf_Form_Field_ShowField('mobile', trlKwf('Mobile')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
        $this->add(new Kwf_Form_Field_ShowField('email', trlKwf('Email')))
            ->setData(new Kwf_Data_Trl_OriginalComponent());
    }
}
