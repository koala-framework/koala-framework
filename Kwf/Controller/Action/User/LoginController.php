<?php
class Kwf_Controller_Action_User_LoginController extends Kwf_Controller_Action
{
    protected function _validateSessionToken()
    {
        if ($this->getRequest()->getActionName() != 'json-logout-user'
            && $this->getRequest()->getActionName() != 'json-login-user'
        ) {
            parent::_validateSessionToken();
        }
    }

    public function indexAction()
    {
        $this->forward('index', 'backend-login');
    }

    public function jsonLoginAction()
    {
        if ($this->_getUserRole() != 'guest') {
            $this->view->message = trlKwf("You don't have enough permissions for this Action");
        }
        $this->view->resource = $this->_getParam('resource');
        $this->view->role = $this->_getParam('role');
        $this->view->login = true;
        $this->view->success = false;

        $this->getResponse()->setRawHeader('HTTP/1.0 401 Access Denied');
        $this->getResponse()->setHttpResponseCode(401);
    }


    public function headerAction()
    {
        try {
            $t = new Kwf_Util_Model_Welcome();
            $row = $t->getRow(1);
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row && $row->getParentRow('LoginImage')) {
            $this->view->image = Kwf_Media::getUrlByRow(
                $row, 'LoginImage'
            );
            $this->view->imageSize = Kwf_Media::getDimensionsByRow($row, 'LoginImage');
        } else {
            $this->view->image = false;
        }
        if (Kwf_Registry::get('config')->allowUntagged === true) {
            if (file_exists('.git') && Kwf_Util_Git::web()->getActiveBranch() != 'production') {
                $this->view->untagged = true;
            }
            if (file_exists(KWF_PATH.'/.git') && Kwf_Util_Git::kwf()->getActiveBranch() != 'production/'.Kwf_Registry::get('config')->application->id) {
                $this->view->untagged = true;
            }
        }
        $this->view->application = Zend_Registry::get('config')->application->toArray();
        $this->_helper->viewRenderer->setRender('loginheader');
    }

    public function showFormAction()
    {
        $this->_helper->viewRenderer->setRender('Login');
        $this->view->ext('');
        $this->view->username = '';
        $this->view->action = Kwf_Setup::getBaseUrl().'/kwf/user/login/show-form';
        if ($this->_getParam('username')) {
            $result = $this->_login();
            $this->view->username = $this->_getParam('username');
            if ($result->isValid()) {
                $this->view->text  = trlKwf('Login successful');
                $this->view->text .= '<!--successful-->';
                $this->view->text .= '<!--sessionToken:'.Kwf_Util_SessionToken::getSessionToken().':-->';
                $this->view->cssClass = 'kwfLoginResultSuccess';
            } else {
                if ($result->getCode() == Zend_Auth_Result::FAILURE_UNCATEGORIZED) {
                    $msgs = $result->getMessages();
                    $this->view->text = $msgs[0];
                    if (isset($msgs[1])) {
                        $asset = new Kwf_Asset('help.png');
                        $this->view->text .= ' <img src="'.$asset.'" width="16" height="16" ext:qwidth="140" ext:qtitel="Hilfe" ext:qtip="'.$msgs[1].'" />';
                    }
                } else {
                    $this->view->text = trlKwf('Login failed');
                }
                $this->view->cssClass = 'kwfLoginResultFailure';

                $msgs = $result->getMessages();
                if ($msgs && isset($msgs[0]) && $msgs[0] == 'IP address not allowed') {
                    $this->view->text .= ' ('.$msgs[0].')';
                }
            }
        } else {
            $this->view->text = '';
        }
    }

    public function activateAction()
    {
        $this->forward('index', 'backend-activate');
    }

    public function lostPasswordAction()
    {
        $this->forward('index', 'backend-lost-password');
    }

    public function logoutAction()
    {
        Kwf_Auth::getInstance()->clearIdentity();
        Kwf_Session::destroy();
        $this->_onLogout();
    }

    public function jsonLoginUserAction()
    {
        $result = $this->_login();
        if (!$result->isValid()) {
            $this->view->error = implode("<br />", $result->getMessages());
        }
        $this->view->sessionToken = Kwf_Util_SessionToken::getSessionToken();
    }

    public function jsonLogoutUserAction()
    {
        $this->logoutAction();
    }

    protected function _createAuthAdapter()
    {
        $adapter = new Kwf_Auth_Adapter_PasswordAuth();
        return $adapter;
    }

    private function _login($username = null, $password = null)
    {
        if (is_null($username)) $username = $this->getRequest()->getParam('username');
        if (is_null($password)) $password = $this->getRequest()->getParam('password');


        $adapter = $this->_createAuthAdapter();

        if (!$adapter instanceof Kwf_Auth_Adapter_PasswordAuth) {
            throw new Kwf_Controller_Exception(('_createAuthAdapter didn\'t return instance of Kwf_Auth_Adapter_PasswordAuth'));
        }

        $auth = Kwf_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        return $auth->authenticate($adapter);
    }

    protected function _onLogin()
    {
    }

    protected function _onLogout()
    {
    }

    public function jsonKeepAliveAction()
    {
        //do nothing
    }
}
