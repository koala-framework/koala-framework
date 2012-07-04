<?php
class Vps_Validate_Digits extends Zend_Validate_Digits
{
    public function __construct()
    {
        $this->_messageTemplates[self::NOT_DIGITS] = trlVps("'%value%' contains characters which are not digits; but only digits are allowed");
        $this->_messageTemplates[self::STRING_EMPTY] = trlVps("'%value%' is an empty string");
        $this->_messageTemplates[self::INVALID] = trlVps("Invalid type given, value should be string, integer or float");
    }
    public function isValid($value)
    {
        $ret = parent::isValid($value);
        return $ret;
    }
}
