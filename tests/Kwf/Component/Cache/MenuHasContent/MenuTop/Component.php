<?php
class Kwf_Component_Cache_MenuHasContent_MenuTop_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 'top';
        return $ret;
    }
}