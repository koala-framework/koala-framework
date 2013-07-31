<?php
class Kwc_Newsletter_Subscribe_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'sub' => 'Kwc_Newsletter_Subscribe_Model'
        );
        $ret['viewCache'] = false;

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return trlKwf('Newsletter subscription');
    }
}
