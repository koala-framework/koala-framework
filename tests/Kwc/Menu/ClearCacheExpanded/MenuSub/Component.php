<?php
class Kwc_Menu_ClearCacheExpanded_MenuSub_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 2;
        return $ret;
    }
}
