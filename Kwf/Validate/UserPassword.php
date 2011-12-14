<?php
/**
 * @package Validate
 */
class Kwf_Validate_UserPassword extends Zend_Validate_Abstract
{
    const NOT_LOGGEDIN = 'notLoggedin';
    const INVALID_PASSWORD = 'invalidPassword';

    public function __construct()
    {
        $this->_messageTemplates[self::NOT_LOGGEDIN] = trlKwf("You are not logged in");
        $this->_messageTemplates[self::INVALID_PASSWORD] = trlKwf("You entered a wrong password");
    }

    public function isValid($value)
    {
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
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
