<?php
class Kwf_Acl_Kwc_TestComponent extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['menuConfig'] = 'Kwf_Acl_Kwc_TestComponent_MenuConfig';
        return $ret;
    }

}
