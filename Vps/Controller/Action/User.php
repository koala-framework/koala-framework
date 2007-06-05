<?php
class Vps_Controller_Action_User extends Vps_Controller_Action_User_Abstract
{
    protected function _createAuthAdapter()
    {
        return new Zend_Auth_Adapter_DbTable(Zend_Registry::get('dao')->getDb(), 'vps_users', 'username', 'password', 'PASSWORD(?)');
    }

    protected function _onLogin($resultRow)
    {
        $userNamespace = new Zend_Session_Namespace('User');
        $userNamespace->role = $resultRow->role;
        $userNamespace->id = $resultRow->id;
    }

    protected function _onLogout()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        $userNamespace->unsetAll();
    }

}
