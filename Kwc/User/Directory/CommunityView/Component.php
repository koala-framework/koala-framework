<?php
class Vpc_User_Directory_CommunityView_Component extends Vpc_Directories_List_ViewPage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['searchForm'] = 'Vpc_User_Directory_CommunityView_SearchForm_Component';
        $ret['generators']['child']['component']['paging'] = 'Vpc_User_Directory_CommunityView_Paging_Component';
        $ret['searchQueryFields'] = array('nickname');
        return $ret;
    }

}
