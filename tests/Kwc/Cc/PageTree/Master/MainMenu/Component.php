<?php
class Kwc_Cc_PageTree_Master_MainMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'main';
        return $ret;
    }
}
