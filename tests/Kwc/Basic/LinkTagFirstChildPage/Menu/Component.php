<?php
class Kwc_Basic_LinkTagFirstChildPage_Menu_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        unset($ret['generators']['subMenu']);
        return $ret;
    }
}
