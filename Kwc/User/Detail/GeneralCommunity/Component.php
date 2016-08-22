<?php
class Kwc_User_Detail_GeneralCommunity_Component extends Kwc_User_Detail_General_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['avatar'] = 'Kwc_User_Detail_GeneralCommunity_Avatar_Component';
        $ret['generators']['child']['component']['rating'] = 'Kwc_User_Detail_GeneralCommunity_Rating_Component';
        $ret['generators']['child']['component']['latestPosts'] = 'Kwc_User_Detail_GeneralCommunity_LastPosts_Component';
        return $ret;
    }
}
