<?php
class Vps_Component_Cache_Menu_Root3_Menu3_Component extends Vpc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        return $ret;
    }
}
