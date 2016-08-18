<?php
class Kwc_Directories_AjaxViewTwoOnOnePage_List1_View_Component extends Kwc_Directories_AjaxViewTwoOnOnePage_View_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['child']['component']['paging']);
        return $ret;
    }
}
