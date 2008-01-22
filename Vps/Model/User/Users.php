<?php

class Vps_Model_User_Users extends Vps_Db_Table
{
    protected $_name = 'vps_users';
    protected $_primary = 'id';
    protected $_rowClass = 'Vps_Model_User_User';

    public function fetchAll($where, $order = null, $limit = null, $start = null)
    {
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

    private function _getRowWebcode()
    {
        return call_user_func(array($this->_rowClass, 'getWebcode'));
    }

    public function login($identity, $credential)
    {
        $restClient = new Vps_Rest_Client();
        $restClient->login($this->_getRowWebcode(), $identity, $credential);

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
        $restClient->exists($this->_getRowWebcode(), $email);
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
        $loginData = Zend_Auth::getInstance()->getStorage()->read();
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
        $storage = Zend_Auth::getInstance()->getStorage();
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
