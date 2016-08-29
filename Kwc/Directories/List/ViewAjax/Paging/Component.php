<?php
class Kwc_Directories_List_ViewAjax_Paging_Component extends Kwc_Paging_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pagesize'] = 25;
        return $ret;
    }
}
