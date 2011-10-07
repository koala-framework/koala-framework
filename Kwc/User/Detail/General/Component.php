<?php
class Kwc_User_Detail_General_Component extends Kwc_User_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = ''; //trlKwf('General');
        return $ret;
    }
}
