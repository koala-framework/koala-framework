<?php
class Vps_Component_Cache_Menu_Root3_Menu2_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 2;
        $ret['maxLevel'] = 2;
        return $ret;
    }
}
