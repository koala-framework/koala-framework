<?php
class Kwf_Component_Cache_Menu_Root2_Menu_Sub_Component extends Kwf_Component_Cache_Menu_Root_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 2;
        return $ret;
    }
}
