<?php
class Vps_Controller_Action_User extends Vps_Controller_Action_User_Abstract
{
    public function loginAction()
    {
        $files[] = VPS_PATH_HTTP . '/Vps/Login/Index.js';
        $files[] = VPS_PATH_HTTP . '/Vps/Login/Dialog.js';
        
        $view = new Vps_View_Smarty(VPS_PATH . '/views');
        $view->assign('files', $files);
        $view->assign('class', 'Vps.Login.Index');
        $body = $view->render('Ext.html');
        $this->getResponse()->appendBody($body);
    }

    public function ajaxLoginAction()
    {
        $username = $this->getRequest()->getParam('username');
        $password = $this->getRequest()->getParam('password');
        $adapter = $this->_createAuthAdapter();

        $auth = Zend_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $result = $auth->authenticate($adapter);

        if (!$result->isValid()) {
            $errors = $result->getMessages();
            $this->getResponse()->appendJson('error', implode("<br />", $errors));
            $success = false;
        } else {
            $this->_onLogin($adapter->getResultRowObject());
            $success = true;
        }
        $this->getResponse()->appendJson('success', $success);
    }
    
    public function ajaxLogoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_onLogout();
        $this->getResponse()->appendJson('success', true);
    }

    protected function _createAuthAdapter()
    {
        return new Zend_Auth_Adapter_DbTable(Zend_Registry::get('dao')->getDb(), 'users', 'username', 'password', 'PASSWORD(?)');
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
