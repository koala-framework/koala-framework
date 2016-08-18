<?php
class Kwc_Mail_Trl_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($param);
        $ret['mailHtmlStyles'] = Kwc_Abstract::getSetting($masterComponentClass, 'mailHtmlStyles');
        $ret['plugins'] = Kwc_Abstract::getSetting($masterComponentClass, 'plugins');

        $ret['recipientSources'] = Kwc_Abstract::getSetting($masterComponentClass, 'recipientSources');

        $ret['fromName'] = Kwc_Abstract::getSetting($masterComponentClass, 'fromName');
        $ret['fromEmail'] = Kwc_Abstract::getSetting($masterComponentClass, 'fromEmail');
        $ret['replyEmail'] = Kwc_Abstract::getSetting($masterComponentClass, 'replyEmail');
        $ret['bcc'] = Kwc_Abstract::getSetting($masterComponentClass, 'bcc');
        $ret['returnPath'] = Kwc_Abstract::getSetting($masterComponentClass, 'returnPath');
        $ret['subject'] = Kwc_Abstract::getSetting($masterComponentClass, 'subject');
        $ret['attachImages'] = Kwc_Abstract::getSetting($masterComponentClass, 'attachImages');
        $ret['trackViews'] = Kwc_Abstract::getSetting($masterComponentClass, 'trackViews');
        $ret['docType'] = Kwc_Abstract::getSetting($masterComponentClass, 'docType');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['parent'] = $this->getData()->parent;
        return $ret;
    }

    public function getHtmlStyles()
    {
        return $this->getData()->parent->getComponent()->getHtmlStyles();
    }

    public function getRecipientSources()
    {
        return $this->getData()->parent->getComponent()->getRecipientSources();
    }

    public function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->getData()->parent->getComponent()->getPlaceholders($recipient);
    }

    protected function _getSubject()
    {
        return $this->getData()->parent->getComponent()->getRow()->subject;
    }
}

