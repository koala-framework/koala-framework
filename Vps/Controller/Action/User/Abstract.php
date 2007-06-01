<?php
class Vps_Controller_Action_User_Abstract extends Vps_Controller_Action
{
    public function loginAction()
    {
        $files[] = '/Vps/Login/Index.js';
        $files[] = '/Vps/Login/Dialog.js';
        
        $view = new Vps_View_Smarty_Ext($files, 'Vps.Login.Index');
        $this->getResponse()->appendBody($view->render(''));
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
    }

    protected function _onLogin($resultRow)
    {
    }

    protected function _onLogout()
    {
    }
}
