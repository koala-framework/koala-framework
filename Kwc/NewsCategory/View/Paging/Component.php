<?php
class Kwc_NewsCategory_View_Paging_Component extends Kwc_Paging_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pagesize'] = 5;
        return $ret;
    }
}
