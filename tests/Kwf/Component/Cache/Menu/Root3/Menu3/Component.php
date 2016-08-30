<?php
class Kwf_Component_Cache_Menu_Root3_Menu3_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 'root';
        return $ret;
    }
}
