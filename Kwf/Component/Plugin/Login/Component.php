<?php
class Kwf_Component_Plugin_Login_Component extends Kwf_Component_Plugin_LoginAbstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['loginForm']['component'] = 'Kwc_User_Login_Component';
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function isLoggedIn()
    {
        if (Kwf_Setup::hasAuthedUser()) {
            $user = Zend_Registry::get('userModel')->getAuthedUser();
            if (!$user) return false;
            if (!$this->_getSetting('validUserRoles')) return true;
            if (in_array($user->role, $this->_getSetting('validUserRoles'))) {
                return true;
            }
        }
        return false;
    }

    public function skipProcessInput(Kwf_Component_Data $data)
    {
        return false;
    }
}
