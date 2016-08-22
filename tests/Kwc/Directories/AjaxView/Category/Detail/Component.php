<?php
class Kwc_Directories_AjaxView_Category_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_Directories_Category_Detail_AjaxViewContentSender';
        $ret['generators']['child']['component']['list'] = 'Kwc_Directories_AjaxView_Category_Detail_List_Component';
        return $ret;
    }
}
