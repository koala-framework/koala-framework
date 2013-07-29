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
            $ret = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED, null, array(trlKwf('no user id'))
            );
            return $ret;
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
                //save facebook_id to userRow
                $userRow->facebook_id = $user['id'];
                $userRow->save();
            } else {
                //we have to create a new user
                $userRow = $users->createUserRow($user['email']);
                $userRow->setSendMails(false);
                $userRow->firstname = $user['first_name'];
                $userRow->lastname = $user['last_name'];
                $userRow->gender = $user['gender'];
                $userRow->facebook_id = $user['id'];
                $userRow->role = 'user';
                $userRow->save();
            }
        }
        $this->_userId = $userRow->id;
        $result = $users->loginUserRow($userRow, true);
        $ret = new Zend_Auth_Result(
            $result['zendAuthResultCode'], $result['identity'], $result['messages']
        );
        return $ret;

    }
}
