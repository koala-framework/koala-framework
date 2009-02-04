<?php
class Vps_Component_Output_Partial_Paging_Paging extends Vpc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 2;
        return $ret;
    }

    public static function getCurrentPageByParam($paramName)
    {
        return 2;
    }
}
?>