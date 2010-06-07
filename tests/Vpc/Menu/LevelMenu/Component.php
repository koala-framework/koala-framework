<?php
class Vpc_Menu_LevelMenu_Component extends Vpc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 3;
        $ret['showAsEditComponent'] = true;
        unset($ret['dataModel']);
        return $ret;
    }
}
