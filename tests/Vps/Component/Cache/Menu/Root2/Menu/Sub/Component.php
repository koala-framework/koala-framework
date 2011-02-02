<?php
class Vps_Component_Cache_Menu_Root2_Menu_Sub_Component extends Vps_Component_Cache_Menu_Root_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 1;
        return $ret;
    }
}
