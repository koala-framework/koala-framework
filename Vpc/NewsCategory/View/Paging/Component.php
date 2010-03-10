<?php
class Vpc_NewsCategory_View_Paging_Component extends Vpc_Paging_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 5;
        return $ret;
    }
}
