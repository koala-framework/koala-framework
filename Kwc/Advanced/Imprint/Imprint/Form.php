<?php
class Kwc_Advanced_Imprint_Imprint_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(200);
        $this->fields->add(new Kwf_Form_Field_TextField('company', trlKwf('Company')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('address', trlKwf('Address')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('zipcode', trlKwf('Zipcode')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('city', trlKwf('City')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('fon', trlKwf('Fon')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('fax', trlKwf('Fax')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('mobile', trlKwf('Mobile')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('email', trlKwf('EMail')))
            ->setWidth(300)
            ->setVtype('email');
        $this->fields->add(new Kwf_Form_Field_TextField('website', trlKwf('Website')))
            ->setWidth(300)
            ->setVtype('url');
        $this->fields->add(new Kwf_Form_Field_TextField('crn', trlKwf('Commercial register number')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('register_court', trlKwf('Register court')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('court', trlKwf('Court')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('uid_number', trlKwf('VAT identification number')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('bank_data', trlKwf('Bank data')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('bank_code', trlKwf('Bank code')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('account_number', trlKwf('Account number')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('iban', trlKwf('IBAN')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('bic_swift', trlKwf('BIC / SWIFT')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('dvr_number', trlKwf('Data handling register number')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('club_number_zvr', trlKwf('Clubnumber ZVR')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextArea('job_title', trlKwf('Job title')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('agency', trlKwf('Agency')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('employment_specification', trlKwf('Employment specification')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_TextField('link_company_az', trlKwf('Entry at WK Austria')))
            ->setWidth(300)
            ->setVtype('url');
    }
}
