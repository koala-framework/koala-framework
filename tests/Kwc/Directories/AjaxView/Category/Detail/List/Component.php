<?php
class Kwc_Directories_AjaxView_Category_Detail_List_Component extends Kwc_Directories_Category_Detail_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_AjaxView_View_Component';
        return $ret;
    }
}
