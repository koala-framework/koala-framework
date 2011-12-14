<?php
/**
 * @package Validate
 */
class Kwf_Validate_Alnum extends Zend_Validate_Alnum
{
    public function __construct($allowWhiteSpace = false)
    {
        parent::__construct($allowWhiteSpace);
        $this->_messageTemplates[self::NOT_ALNUM] = trlKwf("'%value%' has not only alphabetic and digit characters");
        $this->_messageTemplates[self::STRING_EMPTY] = trlKwf("'%value%' is an empty string");
    }
}
