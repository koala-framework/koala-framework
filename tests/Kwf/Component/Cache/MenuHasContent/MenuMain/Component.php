<?php
class Kwf_Component_Cache_MenuHasContent_MenuMain_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        return $ret;
    }
}
