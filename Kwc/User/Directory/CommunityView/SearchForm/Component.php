<?php
class Kwc_User_Directory_CommunityView_SearchForm_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Search');
        $ret['generators']['child']['component']['success'] = false;
        $ret['method'] = 'get';
        return $ret;
    }
}
