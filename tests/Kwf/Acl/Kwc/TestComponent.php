<?php
class Kwf_Acl_Kwc_TestComponent extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        return $ret;
    }

}
