<?php
class Vpc_Advanced_Team_Member_Data_Vcard_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Vpc_Advanced_Team_Member_Data_Vcard_Trl_ContentSender';
        return $ret;
    }
}
