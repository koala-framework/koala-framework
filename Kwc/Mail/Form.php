<?php
class Kwc_Mail_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $default = Kwc_Abstract::getSetting($this->getClass(), 'default');
        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('Subject')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextField('from_email', trlKwf('From Address')))
            ->setVtype('email')
            ->setWidth(300)
            ->setDefaultValue($default['from_email']);
        $this->add(new Kwf_Form_Field_TextField('from_name', trlKwf('From Name')))
            ->setWidth(300)
            ->setDefaultValue($default['from_name']);
        $this->add(new Kwf_Form_Field_TextField('reply_email', trlKwf('Reply Address')))
            ->setVtype('email')
            ->setWidth(300);
    }
}
