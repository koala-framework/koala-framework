<?php
class Vpc_User_LostPassword_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Request password');
        return $ret;
    }
}