<?php
class Kwf_Component_Plugin_Login_Component extends Kwf_Component_Plugin_LoginAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['loginForm']['component'] = 'Kwc_User_Login_Component';
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function isLoggedIn()
    {
        if (Kwf_Setup::hasAuthedUser()) {
            if (!$this->_getSetting('validUserRoles')) return true;
            $user = Zend_Registry::get('userModel')->getAuthedUser();
            if (in_array($user->role, $this->_getSetting('validUserRoles'))) {
                return true;
            }
        }
        return false;
    }

    public function skipProcessInput()
    {
        return false;
    }
}
