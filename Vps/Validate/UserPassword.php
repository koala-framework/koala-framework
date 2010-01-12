<?php
class Vps_Validate_UserPassword extends Zend_Validate_Abstract
{
    const NOT_LOGGEDIN = 'notLoggedin';
    const INVALID_PASSWORD = 'invalidPassword';

    public function __construct()
    {
        $this->_messageTemplates[self::NOT_LOGGEDIN] = trlVps("You are not logged in");
        $this->_messageTemplates[self::INVALID_PASSWORD] = trlVps("You entered a wrong password");
    }

    public function isValid($value)
    {
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$user) {
            $this->_error(self::NOT_LOGGEDIN);
            return false;
        }
        if (!$user->validatePassword($value)) {
            $this->_error(self::INVALID_PASSWORD);
            return false;
        }
        return true;
    }
}
