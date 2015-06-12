<?php
class Kwc_Menu_ClearCacheExpanded_MenuMain_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        return $ret;
    }
}
