<?php
class Vps_Controller_Action_User_LoginController extends Vps_Controller_Action
{
    public function indexAction()
    {
        // ursprünglich $this->_getParam('location'), dann gehen aber GET params verloren
        $location = $_SERVER['REQUEST_URI'];
        if ($location == '') { $location = '/'; }
        $config = array('location' => $location);
        if ($this->_getUserRole() != 'guest') {
            $config['message'] = trlVps("You don't have enough permissions for this Action");
        }
        $this->view->ext('Vps.User.Login.Index', $config);
    }

    public function jsonLoginAction()
    {
        if ($this->_getUserRole() != 'guest') {
            $this->view->message = trlVps("You don't have enough permissions for this Action");
        }
        $this->view->resource = $this->_getParam('resource');
        $this->view->role = $this->_getParam('role');
        $this->view->login = true;
        $this->view->success = false;
    }


    public function headerAction()
    {
        try {
            $t = new Vps_Util_Model_Welcome();
            $row = $t->getRow(1);
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row && $row->getParentRow('LoginImage')) {
            $this->view->image = Vps_Media::getUrlByRow(
                $row, 'LoginImage'
            );
            $this->view->imageSize = Vps_Media::getDimensionsByRow($row, 'LoginImage');
        } else {
            $this->view->image = false;
        }
        if (Vps_Util_Git::web()->getActiveBranch() != 'production'
            || Vps_Util_Git::vps()->getActiveBranch() != 'production/'.Vps_Registry::get('config')->application->id
        ) {
            $this->view->untagged = true;
        }
        $this->view->application = Zend_Registry::get('config')->application->toArray();
        $this->_helper->viewRenderer->setRender('loginheader');
    }

    public function showFormAction()
    {
        $this->_helper->viewRenderer->setRender('Login');
        $this->view->ext('');
        $this->view->username = '';
        if ($this->_getParam('username')) {
            $result = $this->_login();
            $this->view->username = $this->_getParam('username');
            if ($result->isValid()) {
                $this->view->text = trlVps('Login successful').'<!--successful-->';
                $this->view->cssClass = 'vpsLoginResultSuccess';
            } else {
                if ($result->getCode() == Zend_Auth_Result::FAILURE_UNCATEGORIZED) {
                    $msgs = $result->getMessages();
                    $this->view->text = $msgs[0];
                    if (isset($msgs[1])) {
                        $asset = new Vps_Asset('help.png');
                        $this->view->text .= ' <img src="'.$asset.'" width="16" height="16" ext:qwidth="140" ext:qtitel="Hilfe" ext:qtip="'.$msgs[1].'" />';
                    }
                } else {
                    $this->view->text = trlVps('Login failed');
                }
                $this->view->cssClass = 'vpsLoginResultFailure';

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

        $users = Zend_Registry::get('userModel');
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
                $config['errorMsg'] = trlVps('Your account is active and a password has been set.{2}Use the application by {0}clicking here{1}.', array('<a href="/vps/welcome">', '</a>', '<br />'));
            } else {
                $config['errorMsg'] = trlVps('Activation code is invalid. Maybe the URL wasn\'t copied completely?');
            }
        }

        if (empty($config['errorMsg'])) {
            $config['email'] = $row->email;
        }

        $this->view->ext('Vps.User.Activate.Index', $config);
    }

    public function jsonActivateAction()
    {
        $userId = $this->getRequest()->getParam('userId');
        $password = $this->getRequest()->getParam('password');
        $code = $this->getRequest()->getParam('code');

        if (empty($userId) || empty($password) || empty($code)) {
            throw new Vps_ClientException(trlVps('Data not submitted completely.'));
        }

        $users = Zend_Registry::get('userModel');
        $row = $users->getRow($userId);

        if (!$row) {
            throw new Vps_ClientException('User not found in Web.');
        } else if ($row->getActivationCode() != $code) {
            throw new Vps_ClientException(trlVps('Activation code is invalid. Maybe your account has already been activated, the URL was not copied completely, or the password has already been set?'));
        }

        $row->setPassword($password);
        $row->save();

        $this->_login($row->email, $password);
    }

    public function logoutAction()
    {
        Vps_Auth::getInstance()->clearIdentity();
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
        $adapter = new Vps_Auth_Adapter_Service();
        return $adapter;
    }

    private function _login($username = null, $password = null)
    {
        if (is_null($username)) $username = $this->getRequest()->getParam('username');
        if (is_null($password)) $password = $this->getRequest()->getParam('password');


        $adapter = $this->_createAuthAdapter();

        if (!$adapter instanceof Vps_Auth_Adapter_Service) {
            throw new Vps_Controller_Exception(('_createAuthAdapter didn\'t return instance of Vps_Auth_Adapter_Service'));
        }

        $auth = Vps_Auth::getInstance();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $loginData = array();
            $loginData['userId'] = $adapter->getUserId();
            $auth->getStorage()->write($loginData);
        }

        return $result;
    }

    public function jsonLostPasswordAction()
    {
        $email = $this->getRequest()->getParam('email');
        if (!$email) {
            throw new Vps_Exception_Client(trlVps("Please enter your E-Mail-Address"));
        }

        $users = Zend_Registry::get('userModel');
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
