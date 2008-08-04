<?php
class Vpc_User_LostPassword_Formular_ValidateEMail extends Zend_Validate_Abstract
{
    const NOTFOUND = 'emailAddressNotFound';
    public function __construct()
    {
        $this->_messageTemplates[self::NOTFOUND] = trlVps("An account for the address '%value%' doesn't exist");
    }
    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);
        if (!Vps_Registry::get('userModel')->mailExists($value)) {
            $this->_error(self::NOTFOUND);
            return false;
        }
        return true;
    }
}
