<?php
class Kwc_Advanced_Team_Member_Data_Vcard_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Kwc_Advanced_Team_Member_Data_Vcard_Trl_ContentSender';
        return $ret;
    }
}
