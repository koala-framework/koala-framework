<?php
class Vps_Component_Plugin_Login_Component extends Vps_Component_Plugin_Password_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['loginForm']['component'] = 'Vpc_User_Login_Component';
        return $ret;
    }

    public function isLoggedId()
    {
        return !is_null(Zend_Registry::get('userModel')->getAuthedUser());
    }
}
