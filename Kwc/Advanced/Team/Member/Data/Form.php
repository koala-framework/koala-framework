<?php
class Kwc_Advanced_Team_Member_Data_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('firstname', trlKwf('First name')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Last name')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('working_position', trlKwf('Position')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')))
            ->setWidth(400);
        $this->add(new Kwf_Form_Field_TextField('mobile', trlKwf('Mobile')))
            ->setWidth(400);

        if (Kwc_Abstract::getSetting($this->getClass(), 'faxPerPerson')) {
            $this->add(new Kwf_Form_Field_TextField('fax', trlKwf('Fax')))
                ->setWidth(400);
        }
        $this->add(new Kwf_Form_Field_TextField('email', trlKwf('Email')))
            ->setWidth(400);
    }
}
