<?php
class Kwf_Controller_Action_User_LoginController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        // ursprünglich $this->_getParam('location'), dann gehen aber GET params verloren
        $location = $_SERVER['REQUEST_URI'];
        if ($location == '') { $location = '/'; }
        $config = array('location' => $location);
        if ($this->_getUserRole() != 'guest') {
            $config['message'] = trlKwf("You don't have enough permissions for this Action");
        }
        $this->view->ext('Kwf.User.Login.Index', $config);
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
                $this->view->text = trlKwf('Login successful').'<!--successful-->';
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
        $activationCode = $this->_getParam('code');
        list($userId, $code) = explode('-', $activationCode, 2);

        $users = Zend_Registry::get('userModel')->getKwfModel();
        $row = $users->getRow($userId);

        $config = array(
            'errorMsg' => '',
            'userId'   => $userId,
            'code'     => $code
        );

        if (!$row) {
            $config['errorMsg'] = 'User not found in Web.';
        } else if ($row->getActivationCode() != $code) {
            if ($row->password) {
                $config['errorMsg'] = trlKwf('Your account is active and a password has been set.{2}Use the application by {0}clicking here{1}.', array('<a href="/kwf/welcome">', '</a>', '<br />'));
            } else {
                $config['errorMsg'] = trlKwf('Activation code is invalid. Maybe the URL wasn\'t copied completely?');
            }
        }

        if (empty($config['errorMsg'])) {
            $config['email'] = $row->email;
        }

        $this->view->ext('Kwf.User.Activate.Index', $config);
    }

    public function jsonActivateAction()
    {
        $userId = $this->getRequest()->getParam('userId');
        $password = $this->getRequest()->getParam('password');
        $code = $this->getRequest()->getParam('code');

        if (empty($userId) || empty($password) || empty($code)) {
            throw new Kwf_ClientException(trlKwf('Data not submitted completely.'));
        }

        $users = Zend_Registry::get('userModel')->getKwfModel();
        $row = $users->getRow($userId);

        if (!$row) {
            throw new Kwf_ClientException('User not found in Web.');
        } else if ($row->getActivationCode() != $code) {
            throw new Kwf_ClientException(trlKwf('Activation code is invalid. Maybe your account has already been activated, the URL was not copied completely, or the password has already been set?'));
        }

        $validatorClass = Kwf_Registry::get('config')->user->passwordValidator;
        if ($validatorClass) {
            $validator = new $validatorClass();
            $validator->setTranslator(
                new Kwf_Trl_ZendAdapter(Kwf_Trl::getInstance()->getTargetLanguage())
            );
            if (!$validator->isValid($password)) {
                throw new Kwf_ClientException(implode('<br />', $validator->getMessages()));
            }
        }

        $row->setPassword($password);
        $row->save();

        $this->_login($row->email, $password);
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
        $adapter = new Kwf_Auth_Adapter_Service();
        return $adapter;
    }

    private function _login($username = null, $password = null)
    {
        if (is_null($username)) $username = $this->getRequest()->getParam('username');
        if (is_null($password)) $password = $this->getRequest()->getParam('password');


        $adapter = $this->_createAuthAdapter();

        if (!$adapter instanceof Kwf_Auth_Adapter_Service) {
            throw new Kwf_Controller_Exception(('_createAuthAdapter didn\'t return instance of Kwf_Auth_Adapter_Service'));
        }

        $auth = Kwf_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        return $auth->authenticate($adapter);
    }

    public function jsonLostPasswordAction()
    {
        $email = $this->getRequest()->getParam('email');
        if (!$email) {
            throw new Kwf_Exception_Client(trlKwf("Please enter your E-Mail-Address"));
        }

        $users = Zend_Registry::get('userModel')->getKwfModel();
        $result = $users->lostPassword($email);

        $this->view->message = $result;
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
