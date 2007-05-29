<?php
class Vps_Controller_Action_User extends Vps_Controller_Action_User_Abstract
{
    public function loginAction()
    {
        $dao = Zend_Registry::get('dao');
        $db = $dao->getDb();
        $adapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password', 'PASSWORD (?)');
        $this->_showLoginForm($adapter);
    }

    public function logoutAction()
    {
        $this->_logout();
        $this->loginAction();
    }
}
