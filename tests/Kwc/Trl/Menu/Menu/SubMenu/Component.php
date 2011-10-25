<?php
class Kwc_Trl_Menu_Menu_SubMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['dataModel']);
        $ret['level'] = 2;
        return $ret;
    }
}
