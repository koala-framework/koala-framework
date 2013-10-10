<?php
class Kwc_Blog_Box_LastPosts_View_Component extends Kwc_Directories_List_ViewPage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 5;
        $ret['generators']['child']['component']['paging'] = false;
        return $ret;
    }
}
