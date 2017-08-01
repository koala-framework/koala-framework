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
            $this->add(new Kwf_Form_Field_TextField('from_email', trlKwf('From Address')))
                ->setVtype('email')
                ->setWidth(300);
            $this->add(new Kwf_Form_Field_TextField('from_name', trlKwf('From Name')))
                ->setWidth(300);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReplyTo')) {
            $this->add(new Kwf_Form_Field_TextField('reply_email', trlKwf('Reply Address')))
                ->setVtype('email')
                ->setWidth(300);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReturnPath')) {
            $this->add(new Kwf_Form_Field_TextField('return_path', trlKwf('Bounce-Mails to')))
                ->setVtype('email')
                ->setWidth(300);
        }
    }

    public function setEmptyTexts(Kwf_Component_Data $mailComponent)
    {
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFrom')) {
            $this->getByName('from_email')->setEmptyText($mailComponent->getComponent()->getDefaultFromEmail());
            $this->getByName('from_name')->setEmptyText($mailComponent->getComponent()->getDefaultFromName());
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReplyTo')) {
            $this->getByName('reply_email')->setEmptyText($mailComponent->getComponent()->getDefaultReplyTo());
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editReturnPath')) {
            $this->getByName('return_path')->setEmptyText($mailComponent->getComponent()->getDefaultReturnPath());
        }
    }
}
