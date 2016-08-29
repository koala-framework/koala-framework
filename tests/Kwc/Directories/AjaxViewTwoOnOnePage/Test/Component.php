<?php
class Kwc_Directories_AjaxViewTwoOnOnePage_Test_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['list1'] = 'Kwc_Directories_AjaxViewTwoOnOnePage_List1_Component';
        $ret['generators']['child']['component']['list2'] = 'Kwc_Directories_AjaxViewTwoOnOnePage_List2_Component';
        return $ret;
    }
}
