<?php
class Kwf_Component_Cache_Menu_Root3_Menu2_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 2;
        return $ret;
    }
}
