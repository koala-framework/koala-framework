<?php
class Vps_Component_Cache_Menu_Root3_Menu1_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        $ret['maxLevel'] = 2;
        $ret['generators']['subMenu']['component'] = 'Vps_Component_Cache_Menu_Root3_Menu1_Sub_Component';
        return $ret;
    }
}
