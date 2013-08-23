<?php
class RedMallee_Menu_SubDropdown_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = '2';
        $ret['cssClass'] .= ' webListNone';
        return $ret;
    }
}