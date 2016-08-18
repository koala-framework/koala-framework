<?php
class Kwc_List_Switch_ItemPage_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_List_Switch_ItemPage_ContentSender';
        return $ret;
    }
}