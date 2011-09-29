<?php
class Vps_Component_Cache_Menu_Root2_Menu_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        $ret['generators']['subMenu']['component'] = 'Vps_Component_Cache_Menu_Root2_Menu_Sub_Component';
        return $ret;
    }
}
