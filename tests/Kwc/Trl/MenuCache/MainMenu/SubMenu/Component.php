<?php
class Kwc_Trl_MenuCache_MainMenu_SubMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 2;
        $ret['rootElementClass'] .= ' webListNone';
        return $ret;
    }
}