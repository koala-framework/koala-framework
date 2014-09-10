<?php
class Kwf_Validate_Int extends Zend_Validate_Int
{
    public function __construct($locale = null)
    {
        parent::__construct($locale);
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("Invalid type given, value should be string or integer");
        $this->_messageTemplates[self::NOT_INT] = trlKwfStatic("'%value%' does not appear to be an integer");
    }
}
