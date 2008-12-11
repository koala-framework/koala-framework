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
            $b = Vps_Benchmark::start('user-sync gesamt');
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
                    $b3 = Vps_Benchmark::start('user-sync-full all rest request ('.count($ids).' ids)');
                    $response = $restClient->restPost($path,
                        array('method' => 'getData', 'id' => $ids, 'columns' => '')
                    );
                    $ret = new Zend_Rest_Client_Result($response->getBody());
                    if ($b3) $b3->stop();

                    $b4 = Vps_Benchmark::start('user-sync-full sync');
                    $this->_syncUsersByRestData((array)$ret->data);
                    if ($b4) $b4->stop();
                }

                $cacheTimestamp = (string)$ret->timestamp();
            } else {
                $b3 = Vps_Benchmark::start('user-sync-partial rest request');
                $restClient->syncCache($this->getRowWebcode(), $cacheTimestamp);
                $restResult = $restClient->get();
                if ($b3) $b3->stop();

                $b4 = Vps_Benchmark::start('user-sync-partial sync');
                $this->_syncUsersByRestData((array)$restResult->data);
                if ($b4) $b4->stop();

                $cacheTimestamp = (string)$restResult->timestamp;
            }
            $cache->save($cacheTimestamp, $cacheId);
            if ($b) $b->stop();
        }
    }

    private function _syncUsersByRestData($restResultData)
    {
        $restIds = array();
        foreach ($restResultData as $v) {
            $restIds[] = (integer)$v->id;
        }

        if (count($restIds)) {
            $rowset = $this->fetchAll(array('id IN('.implode(',', $restIds).')'));

            foreach ($rowset as $row) {
                foreach ($restResultData as $k => $v) {
                    if ((integer)$v->id == $row->id) {
                        $this->_syncUserByRestData($v, $row);
                        unset($restResultData[$k]);
                        break 1;
                    }
                }
            }
        }
    }

    private function _syncUserByRestData($restRow, $row)
    {
        $cacheCols = call_user_func(array($this->_rowClass, 'getCachedColumns'));
        $cacheData = array();
        foreach ($cacheCols as $c) {
            $cacheData[$c] = (string)$restRow->{$c};
        }
        $row->updateCache($cacheData);
    }

    public function fetchRowByEmail($email)
    {
        if (is_null($email)) {
            throw new Vps_Exception("email must not be null");
        }
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
        if ($credential == 'test' && Vps_Registry::get('config')->debug->testPasswordAllowed) {
            $row = $this->fetchRowByEmail($identity);
            if ($row) {
                return array(
                    'zendAuthResultCode' => Zend_Auth_Result::SUCCESS,
                    'identity'           => $identity,
                    'messages'           => array('Authentication successful.'),
                    'userId'             => $row->id
                );
            }
        }

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

            if (isset($userRow->last_login_web)) {
                $userRow->last_login_web = date('Y-m-d H:i:s');
                $userRow->save();
            }

            return array(
                'zendAuthResultCode' => $restResult->zendAuthResultCode(),
                'identity'           => $identity,
                'messages'           => array($restResult->msg()),
                'userId'             => $restResult->id()
            );
        } else if (strpos($restResult->toValue($restResult->getIterator()), 'Vps_Util_Check_Ip_Exception')) {
            return array(
                'zendAuthResultCode' => Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                'identity'           => $identity,
                'messages'           => array('IP address not allowed')
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
        if ($user = $this->find($userId)->current()) {
            $role = $user->role;
        } else {
            $role = 'guest';
        }
        return $role;
    }
}
