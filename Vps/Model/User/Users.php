<?php
class Vps_Model_User_Users extends Vps_Db_Table
{
    protected $_name = 'vps_users';
    protected $_primary = 'id';
    protected $_rowClass = 'Vps_Model_User_User';
    private $_rowCache = array(); //damit jede row nur einmal erstellt wird

    static public $allCache = null;

    public function fetchAll($where, $order = null, $limit = null, $start = null)
    {
        // wenn serviceColumn in where vorkommt, komplettes where an Service schicken und id's zurückbekommen
        $where = $this->prepareWhere($where);

        // ob nach serviceColumn sortiert werden soll
        if ($order) {
            list($orderColumn, $direction) = explode(' ', $order);
            $orderColumn = trim($orderColumn);

            $sc = call_user_func(array($this->_rowClass, 'getServiceColumns'));
            if (in_array($orderColumn, $sc)) {
                $ret = parent::fetchAll($where);
                $ret->sort($order, $limit, $start);
                return $ret;
            }
        }

        return parent::fetchAll($where, $order, $limit, $start);
    }

    public function prepareWhere($where)
    {
        if ($where) {
            if (!is_array($where)) $where = array($where);
            $sc = call_user_func(array($this->_rowClass, 'getServiceColumns'));

            $serviceColumnMatched = false;
            foreach ($where as $key => $val) {
                foreach ($sc as $scol) {
                    if ((is_string($key) && strpos($key, $scol) !== false)
                        || strpos($val, $scol) !== false
                    ) {
                        $serviceColumnMatched = true;
                        break 2;
                    }
                }
            }

            if ($serviceColumnMatched) {
                $restClient = new Vps_Rest_Client();
                $restClient->getIdsWhere($where, 'fakeArgWegenZend');
                $restResult = $restClient->get();
                $ids = (array)$restResult->ids;
                if (count($ids)) {
                    $where = 'id IN('.implode(',', $ids).')';
                } else {
                    $where = 'id = 0';
                }
            }
        }
        return $where;
    }

    static public function getAllCache()
    {
        return self::$allCache;
    }

    public function createAllCache()
    {
        self::$allCache = array();

        $allIds = array();
        foreach (parent::fetchAll(null) as $row) {
            $allIds[] = $row->id;
        }

        $restClient = new Vps_Rest_Client();
        $restClient->getData($allIds, '');
        $restResult = $restClient->get();

        foreach ($restResult->data as $row) {
            self::$allCache[(int)$row->id] = $row;
        }
    }

    public function fetchRowByEmail($email)
    {
        $restClient = new Vps_Rest_Client();
        $restClient->exists($this->getRowWebcode(), $email);
        $restResult = $restClient->get();
        if ($restResult->status()) {
            return $this->find($restResult->id)->current();
        }
        return null;
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
            throw new Vps_ClientException('User not existent in this web');
        }

        if ($userRow->sendLostPasswordMail()) {
            return 'Activation link sent to email address';
        } else {
            return 'Error sending the mail';
        }
    }

    public function getAuthedUser()
    {
        $loginData = Vps_Auth::getInstance()->getStorage()->read();
        if (!$loginData) return null;
        return $this->find($loginData['userId'])->current();
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

    public function find($id)
    {
        if (!isset($this->_rowCache[$id])) {
            $this->_rowCache[$id] = parent::find($id);
        }
        return $this->_rowCache[$id];
    }
}
