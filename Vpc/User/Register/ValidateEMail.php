<?php
class Vpc_User_Register_ValidateEMail extends Zend_Validate_Abstract
{
    const DUPLICATE = 'emailAddressDuplicate';
    public function __construct()
    {
        $this->_messageTemplates[self::DUPLICATE] = trlVps("An account for the address '%value%' exists allready");
    }
    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);
        if (Vps_Registry::get('userModel')->mailExists($value)) {
            $this->_error(self::DUPLICATE);
            return false;
        }
        return true;
    }
}
