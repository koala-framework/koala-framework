<?php
class Vpc_Advanced_Team_Member_Data_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_TextField('firstname', trlVps('First name')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_TextField('lastname', trlVps('Last name')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_TextField('working_position', trlVps('Position')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_TextField('phone', trlVps('Phone')))
            ->setWidth(400);
        $this->add(new Vps_Form_Field_TextField('mobile', trlVps('Mobile')))
            ->setWidth(400);

        if (Vpc_Abstract::getSetting($this->getClass(), 'faxPerPerson')) {
            $this->add(new Vps_Form_Field_TextField('fax', trlVps('Fax')))
                ->setWidth(400);
        }
        $this->add(new Vps_Form_Field_TextField('email', trlVps('Email')))
            ->setWidth(400);
    }
}
