<?php
class Kwc_Menu_Dropdown_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsDefer']['dep'][] = 'KwfDoubleTapToGo';
        return $ret;
    }
}
