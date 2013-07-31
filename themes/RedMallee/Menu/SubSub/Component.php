<?php
class RedMallee_Menu_SubSub_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 3;
        $ret['cssClass'] .= ' webListNone';
        return $ret;
    }
}