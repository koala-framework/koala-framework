<?php
class Vpc_User_LostPassword_Formular_Component extends Vpc_Formular_Component
{
    protected $_formName = 'Vpc_User_LostPassword_Formular_Form';
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('Request password');
        return $ret;
    }
}