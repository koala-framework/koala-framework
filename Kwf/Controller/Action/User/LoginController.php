<?php
class Kwf_Controller_Action_User_LoginController extends Kwf_Controller_Action
{
    protected function _isAllowedResource()
    {
        return true;
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
        $this->view->role = $this->_getParam('role');
        $this->view->login = true;
        $this->view->success = false;

        $this->getResponse()->setRawHeader('HTTP/1.0 401 Access Denied');
        $this->getResponse()->setHttpResponseCode(401);
    }

    public function jsonGetAuthMethodsAction()
    {
        $users = Zend_Registry::get('userModel');
        $this->view->showPassword = false;
        $this->view->redirects = array();
        foreach ($users->getAuthMethods() as $k=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                $this->view->showPassword = true;
            }
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInBackend()) {
                $url = $this->getFrontController()->getRouter()->assemble(array(
                    'controller' => 'backend-login',
                    'action' => 'redirect',
                ), 'kwf_user');
                $label = $auth->getLoginRedirectLabel();
                $this->view->redirects[] = array(
                    'url' => $url.'?authMethod='.$k,
                    'name' => Kwf_Trl::getInstance()->trlStaticExecute($label['name']),
                    'icon' => isset($label['icon']) ? '/assets/'.$label['icon'] : false,
                    'formOptions' => $auth->getLoginRedirectFormOptions(),
                );
            }
        }
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
            if (file_exists('.git') && (strpos(Kwf_Util_Git::web()->getActiveBranch(), 'production') !== false)) {
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
        $this->view->action = '/kwf/user/login/show-form';
        if ($this->_getParam('username')) {
            $result = $this->_login();
            $this->view->username = $this->_getParam('username');
            if ($result->isValid()) {
                $this->view->text  = trlKwf('Login successful');
                $this->view->text .= '<!--successful-->';
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

    private function _getRedirectBackUrl()
    {
        $redirectBackUrl = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'login',
            'action' => 'redirect-callback',
        ), 'kwf_user');
        $redirectBackUrl = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'
            .$_SERVER['HTTP_HOST']
            .$redirectBackUrl;
        return $redirectBackUrl;
    }

    public function redirectCallbackAction()
    {
        if ($this->_getParam('error')) {
            $this->getRequest()->setParam('errorMessage', $this->_getParam('error_description'));
            $this->forward('error');
            return;
        }

        $state = explode('.', $this->_getParam('state'));

        if (!isset($_COOKIE['kwf-login-redirect']) || $this->_getParam('state') != $_COOKIE['kwf-login-redirect']) {
            if (count($state) == 5 && strpos($state[0], 'activate') === 0) {
                $redirect = urldecode(str_replace('kwfdot', '.', $state[4]));
                if ($redirect !== 'jsCallback') {
                    Kwf_Util_Redirect::redirect($redirect);
                }
            }
            throw new Kwf_Exception("Invalid state");
        }

        if (count($state) < 3) throw new Kwf_Exception_NotFound();
        $action = $state[0]; //login or activate

        $authMethod = $state[1];
        $users = Zend_Registry::get('userModel');
        $authMethods = $users->getAuthMethods();
        if (!isset($authMethods[$authMethod])) {
            throw new Kwf_Exception_NotFound();
        }

        $user = null;
        if ($action == 'login') {
            if (count($state) != 4) throw new Kwf_Exception_NotFound();
            $redirect = urldecode(str_replace('kwfdot', '.', $state[3]));
            try {
                $user = $authMethods[$authMethod]->getUserToLoginByCallbackParams($this->_getRedirectBackUrl(), $this->getRequest()->getParams());
            } catch (Kwf_Exception_Client $e) {
                $this->getRequest()->setParam('redirect', urlencode($redirect));
                $this->getRequest()->setParam('errorMessage', $e->getMessage());
                $this->forward('error');
                return;
            }
        } else if ($action == 'activate') {
            if (count($state) != 5) throw new Kwf_Exception_NotFound();
            $userIdAndCode = $state[3];
            if (!preg_match('#^(.*)-(\w*)$#', $userIdAndCode, $m)) {
                throw new Kwf_Exception_NotFound();
            }
            $userId = $m[1];
            $code = $m[2];
            $redirect = urldecode(str_replace('kwfdot', '.', $state[4]));
            $user = $users->getRow($userId);
            $this->getRequest()->setParam('user', $user);
            if (!$user) {
                $this->getRequest()->setParam('errorMessage', trlKwf("Activation code is invalid. Maybe the URL wasn't copied completely?"));
                $this->forward('error');
                return;
            } else if (!$user->validateActivationToken($code) && $user->isActivated()) {
                $this->getRequest()->setParam('errorMessage', trlKwf("This account has already been activated."));
                $this->forward('error');
                return;
            } else if (!$user->validateActivationToken($code)) {
                $this->getRequest()->setParam('errorMessage', trlKwf("Activation code is invalid. Maybe your account has already been activated, the URL was not copied completely, or the password has already been set?"));
                $this->forward('error');
                return;
            }
            $authMethods[$authMethod]->associateUserByCallbackParams($user, $this->_getRedirectBackUrl(), $this->getRequest()->getParams());
            $user->clearActivationToken();
        }

        if ($user) {
            $users->loginUserRow($user, true);
            if ($redirect == 'jsCallback') {
                echo "<script type=\"text/javascript\">\n";
                echo "window.opener.ssoCallback();\n";
                echo "window.close();\n";
                echo "</script>\n";
                exit;
            } else {
                Kwf_Util_Redirect::redirect($redirect);
            }
        } else {
            $label = $authMethods[$authMethod]->getLoginRedirectLabel();
            $this->getRequest()->setParam('redirect', urlencode($redirect));
            $this->getRequest()->setParam('errorMessage',
                trlKwf("Can't login user, {0} account is not associated with {1}.",
                    array(
                        Kwf_Config::getValue('application.name'),
                        Kwf_Trl::getInstance()->trlStaticExecute($label['name'])
                    )
                )
            );
            $this->forward('error');
        }
    }

    public function authAction()
    {
        $state = $this->_getParam('state');
        if ($state) {
            //we got a state, validate it like it is a redirect-callback
            $this->forward('redirect-callback');
            return;
        }

        $users = Zend_Registry::get('userModel');
        foreach ($users->getAuthMethods() as $authMethod) {
            if ($authMethod instanceof Kwf_User_Auth_Interface_Redirect) {
                $user = $authMethod->getUserToLoginByParams($this->getRequest()->getParams());
                if ($user) {
                    break;
                }
            }
        }
        if ($user) {
            $redirect = $this->_getParam('redirect');
            if (is_array($user)) {
                $redirect = $user['redirect'];
                $user = $user['user'];
            }
            $users->loginUserRow($user, true);
            if (!$redirect) $redirect = '/';
            Kwf_Util_Redirect::redirect($redirect);
        } else {
            throw new Kwf_Exception_AccessDenied();
        }
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

    public function errorAction()
    {
        $this->getHelper('viewRenderer')->setNoController(true);
        $this->getHelper('viewRenderer')->setViewScriptPathNoControllerSpec('user/:action.:suffix');
        $this->view->dep = 'Admin';
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('login-error');
        $this->view->errorMessage = $this->_getParam('errorMessage');
        $redirect = $this->_getParam('redirect');
        if ($redirect == 'jsCallback') {
            $redirect = 'javascript:window.close();';
        }
        $this->view->redirect = $redirect;
    }
}
