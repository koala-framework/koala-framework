<?php
class Vpc_Forum_User_Paging_Component extends Vpc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 25;
        return $ret;
    }
}
