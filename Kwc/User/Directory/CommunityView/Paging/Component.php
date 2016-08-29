<?php
class Kwc_User_Directory_CommunityView_Paging_Component extends Kwc_Paging_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pagesize'] = 20;
        return $ret;
    }

}
