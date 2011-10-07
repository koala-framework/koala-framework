<?php
class Vpc_User_Detail_General_Component extends Vpc_User_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = ''; //trlVps('General');
        return $ret;
    }
}
