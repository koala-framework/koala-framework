<?php
class Kwc_User_Detail_General_Component extends Kwc_User_Detail_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = ''; //trlKwfStatic('General');
        return $ret;
    }
}
