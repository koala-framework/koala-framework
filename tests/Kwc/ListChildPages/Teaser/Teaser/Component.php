<?php
class Kwc_ListChildPages_Teaser_Teaser_Component extends Kwc_List_ChildPages_Teaser_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Basic_Empty_Component';
        $ret['childModel'] = 'Kwc_ListChildPages_Teaser_TestModel';
        return $ret;
    }
}
