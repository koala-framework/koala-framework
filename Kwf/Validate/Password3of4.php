<?php
class Kwf_Validate_Password3of4 extends Zend_Validate_Abstract
{
    const INVALID = 'passwordInvalid';
    const TOO_SHORT = 'passwordTooShort';
    const NO_3OF4 = 'passwordNo3of4';

    protected $_messageTemplates = array();

    public function __construct()
    {
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("The password is invalid");
        $this->_messageTemplates[self::TOO_SHORT] = trlKwfStatic("The password must be at least 16 characters long");
        $this->_messageTemplates[self::NO_3OF4] = trlKwfStatic("Due to security reasons the password must contain 3 out of the following 4 character types: small letters, capital letters, numbers and special signs");
    }

    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        if (mb_strlen($value, 'UTF-8') < (int)Kwf_Config::getValue('user.minimumPasswordLength')) {
            $this->_error(self::TOO_SHORT);
            return false;
        }

        $rulesFulfilled = 0;

        // small letters
        if (preg_match('/[a-z]/', $value)) {
            $rulesFulfilled++;
        }

        // capital letters
        if (preg_match('/[A-Z]/', $value)) {
            $rulesFulfilled++;
        }

        // numbers
        if (preg_match('/[0-9]/', $value)) {
            $rulesFulfilled++;
        }

        // special signs
        if (preg_match('/[+\-*\/\^°"²³§$%&{(\[)\]=}?\\\\ \'`~#\'_.,;:@!]/', $value)) {
            $rulesFulfilled++;
        }

        if ($rulesFulfilled < 3) {
            $this->_error(self::NO_3OF4);
            return false;
        }

        return true;
    }
}
