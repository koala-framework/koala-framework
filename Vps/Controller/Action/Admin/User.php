<?php
class Vps_Controller_Action_Admin_User extends Vps_Controller_Action
{
    public function loginAction()
    {
        $this->view->ext('Vps.Login.Index');
    }

    public function ajaxLoginAction()
    {
        $username = $this->getRequest()->getParam('username');
        $password = $this->getRequest()->getParam('password');
        $adapter = $this->_createAuthAdapter();

        if (!$adapter instanceof Zend_Auth_Adapter_DbTable) {
            throw new Vps_Controller_Exception('_createAuthAdapter didn\'t return instancee of Zend_Auth_Adapter_DbTable');
        }
        
        $auth = Zend_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            $errors = $result->getMessages();
            $this->view->error = implode("<br />", $errors);
        } else {
            $this->_onLogin($adapter->getResultRowObject());
        }

    }
    
    public function ajaxLogoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_onLogout();
    }

    protected function _createAuthAdapter()
    {
        $dao = Zend_Registry::get('dao');
        $adapter = new Zend_Auth_Adapter_DbTable($dao->getDb(), 'vps_users', 'username', 'password', 'PASSWORD(?)');
        return $adapter;
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
