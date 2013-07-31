<?php
// Kwc_Mail_Abstract_Component nötig, weil sich komponenten-links in der mail befinden
class Kwc_Guestbook_Mail_Component extends Kwc_Mail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['recipientSources'] = array(
            'u' => get_class(Kwf_Registry::get('userModel'))
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
        return trlKwf('New entry in your guestbook');
    }
}
