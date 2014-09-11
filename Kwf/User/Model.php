<?php
class Kwf_User_Model extends Kwf_Model_RowCache
{
    protected $_rowClass = 'Kwf_User_Row';
    protected $_authedUser;
    protected $_passwordColumn = 'password';

    protected function _init()
    {
        parent::_init();
        $this->_exprs['name'] = new Kwf_Model_Select_Expr_Concat(array(
            new Kwf_Model_Select_Expr_Field('firstname'),
            new Kwf_Model_Select_Expr_String(' '),
            new Kwf_Model_Select_Expr_Field('lastname'),
        ));
    }
    protected $_cacheColumns = array('email', 'role');

    protected $_columnMappings = array(
        'Kwf_User_UserMapping' => array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'format' => 'format',
            'gender' => 'gender',
            'title' => 'title',
            'role' => 'role'
        )
    );

    public function getUniqueIdentifier()
    {
        return get_class($this);
    }

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel']) && !$this->_proxyModel) {
            $config['proxyModel'] = new Kwf_User_EditModel();
        }
        parent::__construct($config);
    }

    /**
     * @deprecated
     * @see getRowByIdentity
     */
    public function fetchRowByEmail($email)
    {
        return $this->getRowByIdentity($email);
    }

    public function getRowByIdentity($identd)
    {
        if (is_null($identd)) {
            throw new Kwf_Exception("identity must not be null");
        }
        $identdType = 'email';
        if (is_numeric($identd)) {
            $identdType = 'id';
        }

        foreach ($this->getAuthMethods() as $auth) {
            $row = $auth->getRowByIdentity($identd);
            if ($row) return $row;
        }

        return $row;
    }

    public function mailExists($email)
    {
        $row = $this->getRowByIdentity($email);
        return $row ? true : false;
    }
    
    public function loginUserRow($row, $logLogin)
    {
        Kwf_Auth::getInstance()->getStorage()->write(array(
            'userId' => $row->id
        ));
        if ($logLogin) {
            $this->_logLogin($row);
        }
    }

    // if the login didn't happen with the test credentials this function has to be called
    protected function _logLogin(Kwf_Model_Row_Interface $row)
    {
        $this->getProxyModel()->logLogin($row->getProxiedRow());
    }

    /**
     * Overwrite to not use Activate- or Change Password-Component in Frontend
     *
     * e.g. some roles only see backend urls
     *
     * @return boolean
     */
    protected function _allowFrontendUrls($row)
    {
        return true;
    }

    public function getUserActivationUrl($row)
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $lostPasswortComponent = null;
        if ($root && $this->_allowFrontendUrls($row)) {
            // todo: ganz korrekt müsste der Benutzer der anlegt eine Sprache
            // für den Benutzer auswählen
            // oder man leitet auf eine redirect seite um und schaut auf die
            // browser accept language
            $lostPasswortComponent = $root
                ->getComponentByClass('Kwc_User_LostPassword_SetPassword_Component', array('limit' => 1));
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }
        $lostPassUrl = (Kwf_Util_Https::domainSupportsHttps($host) ? 'https' : 'http') . '://'.$host.'/kwf/user/login/activate';
        if ($lostPasswortComponent) $lostPassUrl = $lostPasswortComponent->getAbsoluteUrl(true);
        return $lostPassUrl.'?code='.$row->id.'-'.$row->getActivationCode();
    }

    public function getUserLoginUrl($row)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }
        $url = Kwf_Controller_Front_Component::getInstance()->getWebRouter()->getRoute('admin')->assemble();
        $ret = (Kwf_Util_Https::domainSupportsHttps($host) ? 'https' : 'http') . '://'.$host.'/' . $url;

        $root = Kwf_Component_Data_Root::getInstance();
        if ($root && $this->_allowFrontendUrls($row)) {
            $component = $root->getComponentByClass(
                'Kwc_User_Login_Component', array('limit' => 1)
            );
            if ($component) {
                $ret = $component->getAbsoluteUrl(true);
            }
        }

        return $ret;
    }

    public function lostPassword($email)
    {
        foreach ($this->getAuthMethods() as $auth) {
            $row = $auth->getRowByIdentity($email);
            if ($row) {
                $auth->sendLostPasswordMail($row, $row);
                break;
            }
        }
        if (!$row) {
            throw new Kwf_Exception_Client(trlKwf('User not existent in this web.'));
        }
    }

    public function setPassword($user, $password)
    {
        $user->setPassword($password);
        $this->loginUserRow($user, true);
        return null;
    }

    public function hasAuthedUser()
    {
        return (bool)$this->getAuthedUserId();
    }

    public function getAuthedUserId()
    {
        if (!Kwf_Setup::hasDb()) return null;

        if (php_sapi_name() == 'cli') return null;

        $loginData = Kwf_Auth::getInstance()->getStorage()->read();
        if (!$loginData || !isset($loginData['userId']) || !$loginData['userId']) {
            return null;
        }
        return $loginData['userId'];
    }

    public function getAuthedUser()
    {
        if (!Kwf_Setup::hasDb()) return null;

        if (php_sapi_name() == 'cli') return null;

        if (!$this->_authedUser) {
            $id = $this->getAuthedUserId();
            if ($id) {
                $this->_authedUser = $this->getRow($id);
            }
        }
        return $this->_authedUser;
    }

    public function clearAuthedUser()
    {
        $this->_authedUser = null;
    }

    public function getAuthedUserRole()
    {
        if (php_sapi_name() == 'cli') return 'cli';
        if (!Kwf_Setup::hasDb()) return 'guest';

        $loginData = Kwf_Auth::getInstance()->getStorage()->read();
        if (isset($loginData['userRole'])) {
            return $loginData['userRole'];
        }

        $u = $this->getAuthedUser();
        return $u ? $u->role : 'guest';
    }

    public function getAuthedChangedUserRole()
    {
        $storage = Kwf_Auth::getInstance()->getStorage();
        $loginData = $storage->read();
        $userId = false;
        if (isset($loginData['changeUserId'])) {
            $userId = $loginData['changeUserId'];
        } else if (isset($loginData['userId'])) {
            $userId = $loginData['userId'];
        }
        if ($userId && ($user = $this->getRow($userId))) {
            $role = $user->role;
        } else {
            $role = 'guest';
        }
        return $role;
    }

    public function changeUser($user)
    {
        $storage = Kwf_Auth::getInstance()->getStorage();
        $loginData = $storage->read();
        if (!isset($loginData['changeUserId'])) {
            $loginData['changeUserId'] = $loginData['userId'];
        }
        $loginData['userId'] = $user->id;
        $storage->write($loginData);
    }

    public function getAuthMethods()
    {
        if (!isset($this->_authMethods)) {
            $this->_authMethods = array();
            foreach ($this->getProxyModel()->getAuthMethods($this) as $k=>$auth) {
                if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Proxy_Password(
                        $auth, $this
                    );
                } else if ($auth instanceof Kwf_User_Auth_Interface_AutoLogin) {
                    $this->_authMethods[$k] = new Kwf_User_Auth_Proxy_AutoLogin(
                        $auth, $this
                    );
                } else {
                    throw new Kwf_Exception_NotYetImplemented();
                }
            }
        }
        return $this->_authMethods;
    }
}
