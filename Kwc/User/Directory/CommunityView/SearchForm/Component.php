<?php
class Kwc_User_Directory_CommunityView_SearchForm_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwf('Search');
        $ret['generators']['child']['component']['success'] = false;
        $ret['method'] = 'get';
        return $ret;
    }
}
