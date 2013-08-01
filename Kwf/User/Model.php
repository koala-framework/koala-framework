<?php
class Kwf_User_Model extends Kwf_Model_RowCache implements Kwf_User_ModelInterface
{
    protected $_rowClass = 'Kwf_User_Row';
    protected $_authedUser;
    protected $_passwordColumn = 'password';
    protected $_maxLockTime = 10; // in seconds
    protected $_logActions = true;

    protected $_mailClass = 'Kwf_Mail_Template';

    protected function _init()
    {
        parent::_init();
        $this->_exprs['name'] = new Kwf_Model_Select_Expr_Concat(array(
            new Kwf_Model_Select_Expr_Field('firstname'),
            new Kwf_Model_Select_Expr_String(' '),
            new Kwf_Model_Select_Expr_Field('lastname'),
        ));
    }

    protected $_dependentModels = array(
        'Messages' => 'Kwf_User_MessagesModel'
    );
    protected $_cacheColumns = array('email', 'role');

    private $_lock = null;

    protected $_noLogColumns = array();
    protected $_columnMappings = array(
        'Kwc_Mail_Recipient_Mapping' => array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'format' => 'email_format'
        ),
        'Kwc_Mail_Recipient_GenderMapping' => array(
            'gender' => 'email_gender'
        ),
        'Kwc_Mail_Recipient_TitleMapping' => array(
            'title' => 'title'
        ),
    );

    public function getUniqueIdentifier()
    {
        return get_class($this);
    }

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = new Kwf_Model_Db(array('table'=>'kwf_users'));
        }
        if (isset($config['mailClass'])) {
            $this->_mailClass = $config['mailClass'];
        }
        if (isset($config['log'])) {
            $this->_logActions = $config['log'];
        }
        parent::__construct($config);
    }

    public static function version()
    {
        return 1;
    }

    public function getMailClass()
    {
        return $this->_mailClass;
    }

    // wenn createRow benötigt wird weil man ein anderes userModel (db?) hat,
    // dann kann man diese hier überschreiben und return Kwf_Model_Proxy::createRow($data);
    // zurückgeben
    public function createRow(array $data=array())
    {
        throw new Kwf_Exception("createRow is not allowed in Kwf_User_Model. Use createUserRow() instead.");
    }

    public static function isLockedCreateUser()
    {
        $lock = fopen("temp/create-user.lock", "w");
        $ret = !flock($lock, LOCK_EX | LOCK_NB);
        fclose($lock);
        return $ret;
    }

    public function unlockCreateUser()
    {
        fclose($this->_lock);
        $this->_lock = null;
    }
    public function lockCreateUser()
    {
        if ($this->_lock) {
            throw new Kwf_Exception('Already locked');
        }
        $this->_lock = fopen("temp/create-user.lock", "w");

        $startTime = microtime(true);
        while(true) {
            if (flock($this->_lock, LOCK_EX | LOCK_NB)) {
                break;
            }
            if (microtime(true)-$startTime > $this->_maxLockTime) {
                throw new Kwf_Exception("Lock Failed, locked by");
            }
            usleep(rand(0, 100)*100);
        }
        fwrite($this->_lock, getmypid());
    }

    /**
     * @param string E-Mail address of user
     * @param string webcode parameter used for Service Model (that can have global users)
     */
    public function createUserRow($email, $webcode = null)
    {
        $row = parent::createRow(array('email' => $email));
        $this->_resetPermissions($row);
        return $row;
    }

    /**
     * Setzt die rechte eines neuen users zurück. Meistens wird dies beim Anlegen
     * aus einer Form sowieso überschrieben, aber sicher ist sicher. Hier könnte
     * man zB auch additionalRoles löschen.
     */
    protected function _resetPermissions($row)
    {
        $row->role = 'guest';
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

        $select = $this->select()
            ->whereEquals($identdType, $identd)
            ->whereEquals('deleted', 0);
        $row = $this->getRow($select);

        return $row;
    }

    public function mailExists($email)
    {
        $row = $this->getRowByIdentity($email);
        return $row ? true : false;
    }
    
    public function loginUserRow($row, $logLogin)
    {
        if ($row->locked) {
            $this->writeLog(array(
                'user_id' => $row->id,
                'message_type' => 'wrong_login_locked'
            ));
            return array(
                'zendAuthResultCode' => Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                'identity'           => $row->email,
                'messages'           => array(trlKwf('Account is locked'))
            );
        }
        Kwf_Auth::getInstance()->getStorage()->write(array(
            'userId' => $row->id
        ));
        if ($logLogin) {
            $this->_logLogin($row);
        }

        return array(
            'zendAuthResultCode' => Zend_Auth_Result::SUCCESS,
            'identity'           => $row->email,
            'messages'           => array(trlKwf('Authentication successful')),
            'userId'             => $row->id
        );
    }

    public function login($identity, $credential)
    {
        if (Kwf_Util_Https::supportsHttps() && !isset($_SERVER['HTTPS'])) {
            throw new Kwf_Exception('not on https');
        }
        if ($credential == 'test' && Kwf_Registry::get('config')->debug->testPasswordAllowed) {
            $row = $this->getRowByIdentity($identity);

            if ($row) {
                if ($row->locked) {
                    $this->writeLog(array(
                        'user_id' => $row->id,
                        'message_type' => 'wrong_login_locked'
                    ));
                    return array(
                        'zendAuthResultCode' => Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                        'identity'           => $identity,
                        'messages'           => array(trlKwf('Account is locked'))
                    );
                }
                Kwf_Auth::getInstance()->getStorage()->write(array(
                    'userId' => $row->id
                ));

                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::SUCCESS,
                    'identity'           => $identity,
                    'messages'           => array(trlKwf('Authentication successful')),
                    'userId'             => $row->id
                );
            }
            unset($row);
        }

        $row = $this->getRowByIdentity($identity);
        if (!$row) {
            return array(
                'zendAuthResultCode' => Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                'identity'           => $identity,
                'messages'           => array(trlKwf('User not existent in this web'))
            );
        }

        if ($row->validatePassword($credential)) {
            if ($row->locked) {
                $this->writeLog(array(
                    'user_id' => $row->id,
                    'message_type' => 'wrong_login_locked'
                ));
                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                    'identity'           => $identity,
                    'messages'           => array(trlKwf('Account is locked'))
                );
            }

            // Login nur zählen wenn richtig normal eingeloggt
            $passCol = $this->getPasswordColumn();
            if ($credential == md5($row->$passCol)
                || $row->encodePassword($credential) == $row->$passCol
            ) {
                return $this->loginUserRow($row, true);
            } else {
                return $this->loginUserRow($row, false);
            }
        } else {
            $this->writeLog(array(
                'user_id' => $row->id,
                'message_type' => 'wrong_login_password'
            ));
            return array(
                'zendAuthResultCode' => Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                'identity'           => $identity,
                'messages'           => array(trlKwf('Supplied password is invalid'))
            );
        }
    }

    // if the login didn't happen with the test credentials this function has to be called
    protected function _logLogin($row)
    {
        if (!$row->logins) $row->logins = 0;
        $row->logins = $row->logins + 1;
        $row->last_login = date('Y-m-d H:i:s');
        $row->save();
    }

    public function lostPassword($email)
    {
        $row = $this->getRow($this->select()
            ->whereEquals('email', $email)
            ->whereEquals('deleted', 0)
        );
        if (!$row) {
            throw new Kwf_Exception_Client(trlKwf('User not existent in this web.'));
        }
        if ($row->locked) {
            throw new Kwf_Exception_Client(trlKwf('User is currently locked.'));
        }

        if ($row->sendLostPasswordMail()) {
            return trlKwf('Activation link sent to email address.');
        } else {
            return trlKwf('Error sending the mail.');
        }
    }

    public function setPassword($user, $password)
    {
        Kwf_Auth::getInstance()->clearIdentity();
        $user->setPassword($password);
        $this->_realLoginModifyRow($user);
        $auth = Kwf_Auth::getInstance();
        $auth->getStorage()->write(array('userId' => $user->id));
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

    public function synchronize($overrideMaxSyncDelay = Kwf_Model_MirrorCache::SYNC_AFTER_DELAY)
    {
        //NOOP, implemented in Service_Model
    }

    public function writeLog(array $data)
    {
        if ($this->_logActions) {
            $this->getDependentModel('Messages')->createRow($data)->save();
        }
    }

    public function getPasswordColumn()
    {
        return $this->_passwordColumn;
    }

    public function getNoLogColumns()
    {
        return $this->_noLogColumns;
    }

    public function getKwfModel()
    {
        return $this;
    }
}
