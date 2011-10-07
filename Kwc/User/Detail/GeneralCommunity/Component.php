<?php
class Vpc_User_Detail_GeneralCommunity_Component extends Vpc_User_Detail_General_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['avatar'] = 'Vpc_User_Detail_GeneralCommunity_Avatar_Component';
        $ret['generators']['child']['component']['rating'] = 'Vpc_User_Detail_GeneralCommunity_Rating_Component';
        $ret['generators']['child']['component']['latestPosts'] = 'Vpc_User_Detail_GeneralCommunity_LastPosts_Component';
        return $ret;
    }
}
