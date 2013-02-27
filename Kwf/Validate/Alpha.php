<?php
/**
 * @package Validate
 */
class Kwf_Validate_Alpha extends Zend_Validate_Alpha
{
    public function __construct($allowWhiteSpace = false)
    {
        parent::__construct($allowWhiteSpace);
        $this->_messageTemplates[self::NOT_ALPHA] = trlKwfStatic("'%value%' has not only alphabetic characters");
        $this->_messageTemplates[self::STRING_EMPTY] = trlKwfStatic("'%value%' is an empty string");
    }
}
