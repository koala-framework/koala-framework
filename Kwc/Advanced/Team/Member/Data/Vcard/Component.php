<?php
class Kwc_Advanced_Team_Member_Data_Vcard_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['defaultVcardValues'] = array();
        $ret['contentSender'] = 'Kwc_Advanced_Team_Member_Data_Vcard_ContentSender';
        return $ret;
    }
}
