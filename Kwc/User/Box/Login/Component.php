<?php
class Kwc_User_Box_Login_Component extends Kwc_User_Login_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Box_Login_Form_Component';
        return $ret;
    }
}
