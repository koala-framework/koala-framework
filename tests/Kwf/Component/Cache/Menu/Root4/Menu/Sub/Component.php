<?php
class Kwf_Component_Cache_Menu_Root4_Menu_Sub_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 2;
        return $ret;
    }
}
