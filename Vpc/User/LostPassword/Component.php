<?php
class Vpc_User_LostPassword_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_LostPassword_Form_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}