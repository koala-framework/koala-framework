<?php
class Kwc_Directories_AjaxViewTwoOnOnePage_List1_View_Component extends Kwc_Directories_AjaxViewTwoOnOnePage_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['child']['component']['paging']);
        return $ret;
    }
}
