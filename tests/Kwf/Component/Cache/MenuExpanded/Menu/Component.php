<?php
class Kwf_Component_Cache_MenuExpanded_Menu_Component extends Kwc_Menu_Expanded_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        return $ret;
    }
}