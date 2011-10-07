<?php
class Vpc_User_LostPassword_SetPassword_Component extends Vpc_User_Activate_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] =
            'Vpc_User_LostPassword_SetPassword_Form_Component';
        return $ret;
    }
}
