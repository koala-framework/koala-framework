<?php
class Vpc_Advanced_Imprint_Imprint_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(200);
        $this->fields->add(new Vps_Form_Field_TextField('company', trlVps('Company')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('name', trlVps('Name')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('address', trlVps('Address')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('zipcode', trlVps('Zipcode')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('city', trlVps('City')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('fon', trlVps('Fon')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('fax', trlVps('Fax')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('mobile', trlVps('Mobile')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('email', trlVps('EMail')))
            ->setWidth(300)
            ->setVtype('email');
        $this->fields->add(new Vps_Form_Field_TextField('website', trlVps('Website')))
            ->setWidth(300)
            ->setVtype('url');
        $this->fields->add(new Vps_Form_Field_TextField('crn', trlVps('Commercial register number')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('register_court', trlVps('Register court')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('court', trlVps('Court')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('uid_number', trlVps('Purchase tax-identification number')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('bank_data', trlVps('Bank data')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('bank_code', trlVps('Bank code')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('account_number', trlVps('Account number')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('iban', trlVps('IBAN')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('bic_swift', trlVps('BIC / SWIFT')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('dvr_number', trlVps('DVR-Number')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('club_number_zvr', trlVps('Clubnumber ZVR')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextArea('job_title', trlVps('Job title')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('agency', trlVps('Agency')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('employment_specification', trlVps('Employment specification')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_TextField('link_company_az', trlVps('Entry at WK Austria')))
            ->setWidth(300)
            ->setVtype('url');
    }
}
