<?php
class Kwc_List_Switch_ItemPage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_List_Switch_ItemPage_ContentSender';
        return $ret;
    }
}