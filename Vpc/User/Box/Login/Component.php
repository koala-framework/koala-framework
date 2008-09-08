<?php
class Vpc_User_Box_Login_Component extends Vpc_User_Login_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Box_Login_Form_Component';
        return $ret;
    }
}
