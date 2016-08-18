<?php
class Kwc_Cc_PageTree_Master_MainMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 'main';
        return $ret;
    }
}
