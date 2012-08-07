<?php
class Kwc_User_LostPassword_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Request password');
        return $ret;
    }
}