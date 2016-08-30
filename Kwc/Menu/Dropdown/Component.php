<?php
class Kwc_Menu_Dropdown_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsDefer']['dep'][] = 'KwfDoubleTapToGo';
        return $ret;
    }
}
