<?php
class Vpc_Trl_Menu_Menu_SubMenu_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['showAsEditComponent'] = true;
        unset($ret['dataModel']);
        $ret['level'] = 2;
        return $ret;
    }
}
