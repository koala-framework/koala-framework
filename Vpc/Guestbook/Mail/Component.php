<?php
class Vpc_Guestbook_Mail_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'u' => get_class(Vps_Registry::get('userModel'))
        );

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret = array_merge($ret, $this->getMailData());
        return $ret;
    }

    public function getSubject(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        return trlVps('New entry in your guestbook');
    }
}
