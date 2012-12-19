<?php
class Kwc_Menu_IsVisibleDynamic_Root_Menu_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        unset($ret['generators']['subMenu']);
        return $ret;
    }
}
