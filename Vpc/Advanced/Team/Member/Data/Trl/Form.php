<?php
class Vpc_Advanced_Team_Member_Data_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_ShowField('title', trlVps('Title')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
        $this->add(new Vps_Form_Field_ShowField('firstname', trlVps('First name')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
        $this->add(new Vps_Form_Field_ShowField('lastname', trlVps('Last name')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
        $this->add(new Vps_Form_Field_TextField('working_position', trlVps('Position')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_ShowField('phone', trlVps('Phone')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
        $this->add(new Vps_Form_Field_ShowField('mobile', trlVps('Mobile')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
        $this->add(new Vps_Form_Field_ShowField('email', trlVps('Email')))
            ->setData(new Vps_Data_Trl_OriginalComponent());
    }
}
