<?php
class Kwc_User_Directory_CommunityView_Component extends Kwc_Directories_List_ViewPage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_User_Directory_CommunityView_SearchForm_Component';
        $ret['generators']['child']['component']['paging'] = 'Kwc_User_Directory_CommunityView_Paging_Component';
        $ret['searchQueryFields'] = array('nickname');
        return $ret;
    }

}
