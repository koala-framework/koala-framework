<?php
class Vpc_Menu_Menu_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['subMenu']['component'] = 'Vpc_Menu_Menu_Component';
        $ret['showAsEditComponent'] = true;
        unset($ret['dataModel']);
        $ret['level'] = 'root';
        $ret['maxLevel'] = 2;
        return $ret;
    }
}
