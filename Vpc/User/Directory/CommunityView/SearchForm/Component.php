<?php
class Vpc_User_Directory_CommunityView_SearchForm_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        $ret['method'] = 'get';
        return $ret;
    }
}
