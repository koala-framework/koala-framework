<?php
/**
 * @package Validate
 */
class Kwf_Validate_Digits extends Zend_Validate_Digits
{
    public function __construct()
    {
        $this->_messageTemplates[self::NOT_DIGITS] = trlKwfStatic("'%value%' contains characters which are not digits; but only digits are allowed");
        $this->_messageTemplates[self::STRING_EMPTY] = trlKwfStatic("'%value%' is an empty string");
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("Invalid type given, value should be string, integer or float");
    }
    public function isValid($value)
    {
        $ret = parent::isValid($value);
        return $ret;
    }
}
