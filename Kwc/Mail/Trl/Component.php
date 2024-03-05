<?php
class Kwc_Mail_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['generators']['mail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Mail_Trl_Mail_Component.' . $masterComponentClass
        );
        return $ret;
    }

    public function send(Kwc_Mail_Recipient_Interface $recipient, $data = null, $toAddress = null, $format = null, $addViewTracker = true)
    {
        return $this->getData()->getChildComponent('_mail')->getComponent()->send(
            $recipient, $data, $toAddress, $format, $addViewTracker
        );
    }

    public function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->getData()->chained->getComponent()->getPlaceholders($recipient);
    }

    public function getRecipientSources()
    {
        return $this->getData()->chained->getComponent()->getRecipientSources();
    }

    public function getHtmlStyles()
    {
        return $this->getData()->chained->getComponent()->getHtmlStyles();
    }

    public function getRecipient()
    {
        return $this->getData()->chained->getComponent()->getRecipient();
    }
}
