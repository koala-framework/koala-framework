<?php
class Vps_Acl_Vpc_TestComponent extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        return $ret;
    }

}
