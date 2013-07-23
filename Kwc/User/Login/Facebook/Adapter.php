<?php
class Kwc_User_Login_Facebook_Adapter implements Zend_Auth_Adapter_Interface
{
    protected $_token = null;

    protected $_userId = null;

    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function authenticate()
    {
        if (empty($this->_token)) {
            throw new Kwf_Exception('A value for the token was not provided prior to authentication with Kwc_User_Login_Facebook_Adapter.');
        }
        $facebook = Kwf_Util_Facebook_Api::getInstance();
        $facebook->setAccessToken($this->_token);
        $userId = $facebook->getUser();
        if (!$userId) {
            throw new Kwf_Exception('no user id');
        }
        $user = $facebook->api('/me', 'GET');
        $users = Zend_Registry::get('userModel')->getKwfModel();
        $s = new Kwf_Model_Select();
        $s->whereEquals('deleted', false);
        $s->whereEquals('facebook_id', $user['id']);
        $userRow = $users->getRow($s);
        if (!$userRow) {
            //user has not allready logged in before via facebook
            $s = new Kwf_Model_Select();
            $s->whereEquals('deleted', false);
            $s->whereEquals('email', $user['email']);
            $userRow = $users->getRow($s);
            if ($userRow) {
                if ($userRow->locked) {
                    $this->writeLog(array(
                        'user_id' => $userRow->id,
                        'message_type' => 'wrong_login_locked'
                    ));
                    $ret = new Zend_Auth_Result(
                        Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_userId, array(trlKwf('Account is locked'))
                    );
                    return $ret;
                }
                //save facebook_id to userRow
                $userRow->facebook_id = $user['id'];
                $userRow->save();
            } else {
                //we have to create a new user
                $userRow = $users->createUserRow($user['email']);
                $userRow->setSendMails(false);
                $userRow->firstname = $user['first_name'];
                $userRow->lastname = $user['last_name'];
                $userRow->facebook_id = $user['id'];
                $userRow->role = 'user';
                $userRow->save();
            }
        }

        $this->_userId = $userRow->id;
        Kwf_Auth::getInstance()->getStorage()->write(array(
            'userId' => $this->_userId
        ));

        if (!$userRow->logins) $userRow->logins = 0;
        $userRow->logins = $userRow->logins + 1;
        $userRow->last_login = date('Y-m-d H:i:s');
        $userRow->save();
        $ret = new Zend_Auth_Result(
            Zend_Auth_Result::SUCCESS, $this->_userId, array(trlKwf('Authentication successful'))
        );
        return $ret;

    }
}
