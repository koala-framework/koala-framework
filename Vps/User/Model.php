<?php
class Vps_User_Model extends Vps_Model_RowCache
{
    protected $_rowClass = 'Vps_User_Row';
    protected $_authedUser;
    protected $_passwordColumn = 'password';
    protected $_maxLockTime = 10; // in seconds
    protected $_logActions = true;

    protected $_mailClass = 'Vps_Mail_Template';

    protected $_dependentModels = array(
        'Messages' => 'Vps_User_MessagesModel'
    );
    protected $_cacheColumns = array('email', 'role');

    private $_lock = null;

    protected $_noLogColumns = array();

    public function getUniqueIdentifier()
    {
        return get_class($this);
    }

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = 'Vps_User_Mirror';
        }
        $this->_siblingModels['webuser'] = 'Vps_User_Web_Model';
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
    // dann kann man diese hier überschreiben und return Vps_Model_Proxy::createRow($data);
    // zurückgeben
    public function createRow(array $data=array())
    {
        throw new Vps_Exception("createRow is not allowed in Vps_User_Model. Use createUserRow() instead.");
    }

    public static function isLockedCreateUser()
    {
        $lock = fopen("application/temp/create-user.lock", "w");
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
            throw new Vps_Exception('Already locked');
        }
        $this->_lock = fopen("application/temp/create-user.lock", "w");

        $startTime = microtime(true); 
        while(true) {
            if (flock($this->_lock, LOCK_EX | LOCK_NB)) {
                break;
            }
            if (microtime(true)-$startTime > $this->_maxLockTime) {
                throw new Vps_Exception("Lock Failed, locked by");
            }
            usleep(rand(0, 100)*100);
        }
        fwrite($this->_lock, getmypid());
    }

    public function createUserRow($email, $webcode = null)
    {
        if (is_null($webcode)) {
            $webcode = self::getWebcode();
        }

        if (empty($webcode) && !is_null($webcode) && $email) {
            $this->lockCreateUser();
            $row = $this->getRow($this->select()
                ->whereEquals('email', $email)
                ->whereEquals('webcode', '')
            );
            if ($row) {
                if (!$row->deleted) {
                    $this->unlockCreateUser();
                    throw new Vps_Exception_Client(
                        trlVps('An account with this email address already exists')
                    );
                }
                // global user wurde gelöscht und wird wieder angelegt
                $row->locked = 0;
                $row->deleted = 0;
                $this->_resetPermissions($row);
                $this->unlockCreateUser();
                return $row;
            } else {
                // globaler benutzer existiert im web noch nicht. schauen, ob
                // es ihn bereits gibt, sonst komplett neu anlegen
                $allModel = Vps_Model_Abstract::getInstance('Vps_User_All_Model');
                $allRow = $allModel->getRow($allModel->select()
                    ->whereEquals('email', $email)
                    ->whereEquals('webcode', '')
                );
                if ($allRow) {
                    $relationModel = Vps_Model_Abstract::getInstance('Vps_User_Relation_Model');
                    $relRow = $relationModel->createRow();
                    $relRow->user_id = $allRow->id;
                    $relRow->locked = 0;
                    $relRow->deleted = 0;
                    $relRow->save();

                    $allRow->forceSave(); // damit last_modified geschrieben wird

                    $this->getProxyModel()->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);

                    $this->unlockCreateUser();

                    $row = $this->getRow($this->select()
                        ->whereEquals('email', $email)
                        ->whereEquals('webcode', '')
                    );
                    $this->_resetPermissions($row);
                    $row->setNotifyGlobalUserAdded(true);
                    return $row;
                }
            }
            $this->unlockCreateUser();
        }

        $row = parent::createRow(array('email' => $email, 'webcode' => $webcode));
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

    public static function getWebcode()
    {
        $webCode = Vps_Registry::get('config')->service->users->webcode;
        if (is_null($webCode)) {
            throw new Vps_Exception("'service.users.webcode' not defined in config");
        }
        return $webCode;
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
            throw new Vps_Exception("identity must not be null");
        }
        $identdType = 'email';
        if (is_numeric($identd)) {
            $identdType = 'id';
        }

        $select = $this->select()
            ->whereEquals($identdType, $identd)
            ->whereEquals('webcode', $this->getRowWebcode())
            ->whereEquals('deleted', 0);
        $row = $this->getRow($select);
        if (!$row) {
            $select = $this->select()
                ->whereEquals($identdType, $identd)
                ->whereEquals('webcode', '')
                ->whereEquals('deleted', 0);
            $row = $this->getRow($select);
        }

        return $row;
    }

    public function mailExists($email)
    {
        $row = $this->getRowByIdentity($email);
        return $row ? true : false;
    }

    public function getRowWebcode()
    {
        return call_user_func(array($this->_rowClass, 'getWebcode'));
    }

    public function login($identity, $credential)
    {
        if ($credential == 'test' && Vps_Registry::get('config')->debug->testPasswordAllowed) {
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
                        'messages'           => array(trlVps('Account is locked'))
                    );
                }

                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::SUCCESS,
                    'identity'           => $identity,
                    'messages'           => array(trlVps('Authentication successful')),
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
                'messages'           => array(trlVps('User not existent in this web'))
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
                    'messages'           => array(trlVps('Account is locked'))
                );
            }

            // Login nur zählen wenn richtig normal eingeloggt
            $passCol = $this->getPasswordColumn();
            if ($credential == md5($row->$passCol)
                || $row->encodePassword($credential) == $row->$passCol
            ) {
                $this->_realLoginModifyRow($row);
            }

            return array(
                'zendAuthResultCode' => Zend_Auth_Result::SUCCESS,
                'identity'           => $identity,
                'messages'           => array(trlVps('Authentication successful')),
                'userId'             => $row->id
            );
        } else {
            $this->writeLog(array(
                'user_id' => $row->id,
                'message_type' => 'wrong_login_password'
            ));
            return array(
                'zendAuthResultCode' => Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                'identity'           => $identity,
                'messages'           => array(trlVps('Supplied password is invalid'))
            );
        }
    }

    // wird nur aufgerufen, wenn man sich mit den richtigen Daten eingeloggt hat
    protected function _realLoginModifyRow($row)
    {
        if (!$row->logins) $row->logins = 0;
        $row->logins = $row->logins + 1;
        $row->last_login = date('Y-m-d H:i:s');
        if (isset($userRow->last_login_web)) {
            $userRow->last_login_web = date('Y-m-d H:i:s');
        }
        $row->save();
    }

    public function lostPassword($email)
    {
        $row = $this->getRow($this->select()->whereEquals('email', $email));
        if (!$row) {
            throw new Vps_ClientException(trlVps('User not existent in this web.'));
        }

        if ($row->sendLostPasswordMail()) {
            return trlVps('Activation link sent to email address.');
        } else {
            return trlVps('Error sending the mail.');
        }
    }

    public function getAuthedUser()
    {
        if (!Vps_Setup::hasDb()) return null;

        if (php_sapi_name() == 'cli') return null;

        if (!$this->_authedUser) {
            $loginData = Vps_Auth::getInstance()->getStorage()->read();
            if (!$loginData || !isset($loginData['userId']) || !$loginData['userId']) {
                return null;
            }
            $this->_authedUser = $this->getRow($loginData['userId']);
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
        if (!Vps_Setup::hasDb()) return 'guest';

        $loginData = Vps_Auth::getInstance()->getStorage()->read();
        if (isset($loginData['userRole'])) {
            return $loginData['userRole'];
        }

        $u = $this->getAuthedUser();
        return $u ? $u->role : 'guest';
    }

    public function getAuthedChangedUserRole()
    {
        $storage = Vps_Auth::getInstance()->getStorage();
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

    public function synchronize($overrideMaxSyncDelay = Vps_Model_MirrorCache::SYNC_AFTER_DELAY)
    {
        $this->getProxyModel()->synchronize($overrideMaxSyncDelay);
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

}
