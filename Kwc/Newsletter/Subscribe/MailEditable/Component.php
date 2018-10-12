<?php
class Kwc_Newsletter_Subscribe_MailEditable_Component extends Kwc_Mail_Editable_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['recipientSources']['sub'] = 'Kwc_Newsletter_Subscribe_Model';
        $ret['generators']['content']['component'] = 'Kwc_Newsletter_Subscribe_MailEditable_Content_Component';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($mailData = $this->getMailData()) $ret = array_merge($ret, $mailData);

        if (!array_key_exists('doubleOptInComponent', $ret)) {
            $ret['doubleOptInComponent'] = $this->getData()->parent->getChildComponent('_doubleOptIn');
        }

        if (!array_key_exists('editComponent', $ret)) {
            $nlData = $this->getData()->parent->getComponent()->getSubscribeToNewsletterComponent();
            $ret['editComponent'] = $nlData->getChildComponent('_editSubscriber');
        }
        return $ret;
    }

    public function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = parent::getPlaceholders($recipient);
        if ($recipient) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $ret['host'] = $_SERVER['HTTP_HOST'];
            } else {
                $ret['host'] = Kwf_Registry::get('config')->server->domain;
            }
        }
        return $ret;
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->getData()->trlKwf('Newsletter subscription');
    }

    public function getNameForEdit()
    {
        return $this->getData()->trlKwf('Subscription') . ' (' . $this->getData()->getDomainComponent()->name . ')';
    }
}
