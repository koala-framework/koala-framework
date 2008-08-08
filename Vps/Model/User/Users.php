<?php
class Vps_Model_User_Users extends Vps_Db_Table
{
    protected $_name = 'vps_users';
    protected $_primary = 'id';
    protected $_rowClass = 'Vps_Model_User_User';
    private $_authedUser;

    static $checkCacheDone = false;

    protected function _fetch($select)
    {
        $serviceCols = call_user_func(array($this->_rowClass, 'getServiceColumns'));
        if ($select instanceof Zend_Db_Select && preg_match('/('.implode('|', $serviceCols).')/', $select->__toString())) {
            $this->checkCache();
        }

        return parent::_fetch($select);
    }

    public function checkCache()
    {
        if (!self::$checkCacheDone) {
            $b = Vps_Benchmark::start();
            self::$checkCacheDone = true;

            $cacheId = 'userDbCache';
            $frontendOptions = array('lifetime' => null);
            $backendOptions = array('cache_dir' => 'application/cache/table/');
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

            $restClient = new Vps_Rest_Client();
            if (!($cacheTimestamp = $cache->load($cacheId))) {
                if (preg_match('/^(.+)\/([^\/]+)\/?$/', $restClient->getUri()->getUri(), $matches)) {
                    $restClient->setUri($matches[1]);
                    $path = $matches[2];
                } else {
                    throw new Vps_Exception('Rest URL '.$restClient->getUri()->getUri().' not possible. '
                        .'Use URL of type http://service.vivid-planet.com/user');
                }

                // alle ids mitschicken
                $info = $this->info();
                $select = $this->getAdapter()->select();
                $select->from($info['name'], 'id');
                $idRows = $this->getAdapter()->fetchAll($select);

                $ids = array();
                foreach ($idRows as $v) {
                    $ids[] = $v['id'];
                }

                if (count($ids)) {
                    $response = $restClient->restPost($path,
                        array('method' => 'getData', 'id' => $ids, 'columns' => '')
                    );

                    $ret = new Zend_Rest_Client_Result($response->getBody());

                    foreach ($ret->data as $k => $v) {
                        $this->_syncUserByRestData($v);
                    }
                }

                $cacheTimestamp = (string)$ret->timestamp();
            } else {
                $restClient->syncCache($this->getRowWebcode(), $cacheTimestamp);
                $restResult = $restClient->get();

                foreach ((array)$restResult->data as $v) {
                    $this->_syncUserByRestData($v);
                }

                $cacheTimestamp = (string)$restResult->timestamp;
            }
            $cache->save($cacheTimestamp, $cacheId);
        }
    }

    private function _syncUserByRestData($restRow)
    {
        $row = $this->find((integer)$restRow->id)->current();
        if ($row) {
            $cacheCols = call_user_func(array($this->_rowClass, 'getCachedColumns'));
            $cacheData = array();
            foreach ($cacheCols as $c) {
                $cacheData[$c] = (string)$restRow->{$c};
            }
            $row->updateCache($cacheData);
        }
    }

    public function fetchRowByEmail($email)
    {
        return $this->fetchRow(array('email = ?' => $email));
    }

    public function mailExists($email)
    {
        $row = $this->fetchRowByEmail($email);
        return $row ? true : false;
    }

    public function getRowWebcode()
    {
        return call_user_func(array($this->_rowClass, 'getWebcode'));
    }

    public function login($identity, $credential)
    {
        $restClient = new Vps_Rest_Client();
        $restClient->login($this->getRowWebcode(), $identity, $credential);

        $restResult = $restClient->get();

        if ($restResult->status()) {
            $userRow = $this->find($restResult->id())->current();

            if (!$userRow) {
                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                    'identity'           => $identity,
                    'messages'           => array('User not existent in this web.')
                );
            }

            return array(
                'zendAuthResultCode' => $restResult->zendAuthResultCode(),
                'identity'           => $identity,
                'messages'           => array($restResult->msg()),
                'userId'             => $restResult->id()
            );
        } else {
            return array(
                'zendAuthResultCode' => $restResult->zendAuthResultCode(),
                'identity'           => $identity,
                'messages'           => array($restResult->msg())
            );
        }
    }

    public function lostPassword($email)
    {
        // ID des benutzers vom Service ermitteln
        $restClient = new Vps_Rest_Client();
        $restClient->exists($this->getRowWebcode(), $email);
        $restResult = $restClient->get();

        if (!$restResult->status()) {
            throw new Vps_ClientException($restResult->msg());
        }

        // Prüfen ob die ID im Web erlaubnis hat
        $userRow = $this->find($restResult->id())->current();
        if (!$userRow) {
            throw new Vps_ClientException(trlVps('User not existent in this web.'));
        }

        if ($userRow->sendLostPasswordMail()) {
            return trlVps('Activation link sent to email address.');
        } else {
            return trlVps('Error sending the mail.');
        }
    }

    public function getAuthedUser()
    {
        if (!isset($this->_authedUser)) {
            $loginData = Vps_Auth::getInstance()->getStorage()->read();
            if (!$loginData) return null;
            $this->_authedUser = $this->find($loginData['userId'])->current();
        }
        return $this->_authedUser;
    }

    public function getAuthedUserRole()
    {
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
        if ($user = $this->find($userId)->current()) {
            $role = $user->role;
        } else {
            $role = 'guest';
        }
        return $role;
    }
}
