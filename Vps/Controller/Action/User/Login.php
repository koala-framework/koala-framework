<?php
class Vps_Controller_Action_User_Login extends Vps_Controller_Action
{
    public function indexAction()
    {
        $location = $this->_getParam('location');
        $controllerUrl = $this->_getParam('controllerUrl');
        if (!$controllerUrl) { $controllerUrl = ''; }
        if ($location == '') { $location = '/'; }
        $config = array('location' => $location, 'controllerUrl' => $controllerUrl);
        $this->view->ext('Vps.User.Login.Index', $config);
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_onLogout();
    }

    public function jsonLoginAction()
    {
        $this->view->login = true;
        $this->view->success = false;
    }

    public function jsonLoginUserAction()
    {
        $username = $this->getRequest()->getParam('username');
        $password = $this->getRequest()->getParam('password');
        $adapter = $this->_createAuthAdapter();

        if (!$adapter instanceof Zend_Auth_Adapter_DbTable) {
            throw new Vps_Controller_Exception('_createAuthAdapter didn\'t return instance of Zend_Auth_Adapter_DbTable');
        }
        
        $auth = Zend_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            $this->view->error = implode("<br />", $result->getMessages());
        } else {
            $resultRow = $adapter->getResultRowObject(null, array('password', 'password_salt'));
            $auth->getStorage()->write($resultRow);
            $this->_onLogin();
        }

    }
    
    public function jsonLogoutUserAction()
    {
        $this->logoutAction();
    }
    
    protected function _createAuthAdapter()
    {
        $dao = Zend_Registry::get('dao');
        $adapter = new Zend_Auth_Adapter_DbTable($dao->getDb(), 'users', 'username', 'password', 'MD5(CONCAT(?, password_salt))');
        return $adapter;
    }

    protected function _onLogin()
    {
    }

    protected function _onLogout()
    {
    }
}
