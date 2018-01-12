<?php
class Kwf_Validate_Float extends Zend_Validate_Float
{
    public function __construct($locale = null)
    {
        parent::__construct($locale);
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("Invalid type given. String, integer or float expected");
        $this->_messageTemplates[self::NOT_FLOAT] = trlKwfStatic("'%value%' does not appear to be a float");
    }
}
