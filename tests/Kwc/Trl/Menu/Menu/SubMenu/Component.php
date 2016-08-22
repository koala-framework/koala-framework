<?php
class Kwc_Trl_Menu_Menu_SubMenu_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['dataModel']);
        $ret['level'] = 2;
        return $ret;
    }
}
