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
        $s->whereEquals('facebook_id', $user['id']);
        $result = $users->getRow($s);
        if (!$result) {
            //user has not allready logged in before via facebook
            $s = new Kwf_Model_Select();
            $s->whereEquals('email', $user['email']);
            $result = $users->getRow($s);
            if ($result) {
                if ($result->locked) {
                    $this->writeLog(array(
                        'user_id' => $result->id,
                        'message_type' => 'wrong_login_locked'
                    ));
                    return null;
                }
                //save facebook_id to userRow
                $result->facebook_id = $user['id'];
                $result->save();
            } else {
                //we have to create a new user
                $result = $users->createUserRow($user['email']);
                $result->firstname = $user['first_name'];
                $result->lastname = $user['last_name'];
                $result->facebook_id = $user['id'];
                $result->role = 'user';
                $result->save();
            }
        }

        $this->_userId = $result->id;
        Kwf_Auth::getInstance()->getStorage()->write(array(
            'userId' => $this->_userId
        ));

        if (!$result->logins) $result->logins = 0;
        $result->logins = $result->logins + 1;
        $result->last_login = date('Y-m-d H:i:s');
        $result->save();

    }
}
