<?php
class Kwc_Directories_Item_Detail_AssignedCategories_View_Component
    extends Kwc_Directories_List_ViewPage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['paging'] = false;
        return $ret;
    }
}
