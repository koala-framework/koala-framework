<?php
class Vpc_User_Directory_CommunityView_Paging_Component extends Vpc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 20;
        return $ret;
    }

}
