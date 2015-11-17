<?php
class Kwc_Mail_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('subject', trlKwf('Subject')))
            ->setAllowBlank(false)
            ->setWidth(300);
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFrom')) {
            $defaultFromEmail = Kwc_Abstract::getSetting($this->getClass(), 'fromEmail');
            $this->add(new Kwf_Form_Field_TextField('from_email', trlKwf('From Address')))
                ->setVtype('email')
                ->setWidth(300)
                ->setEmptyText($defaultFromEmail);
            $defaultFromName = Kwc_Abstract::getSetting($this->getClass(), 'fromName');
            $this->add(new Kwf_Form_Field_TextField('from_name', trlKwf('From Name')))
                ->setWidth(300)
                ->setEmptyText($defaultFromName);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReplyTo')) {
            $defaultReplyEmail = Kwc_Abstract::getSetting($this->getClass(), 'replyEmail');
            $this->add(new Kwf_Form_Field_TextField('reply_email', trlKwf('Reply Address')))
                ->setVtype('email')
                ->setWidth(300)
                ->setEmptyText($defaultReplyEmail);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReturnPath')) {
            $defaultReturnPath = Kwc_Abstract::getSetting($this->getClass(), 'returnPath');
            $this->add(new Kwf_Form_Field_TextField('return_path', trlKwf('Bounce-Mails to')))
                ->setVtype('email')
                ->setWidth(300)
                ->setEmptyText($defaultReturnPath);
        }
    }
}
