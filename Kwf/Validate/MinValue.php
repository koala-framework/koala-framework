<?php
/**
 * @package Validate
 */
class Kwf_Validate_MinValue extends Zend_Validate_GreaterThan
{
    public function __construct($min)
    {
        $this->_messageTemplates[self::NOT_GREATER] = trlKwfStatic("'%value%' must be greater or equal than '%min%'");
        parent::__construct($min);
    }

    public function isValid($value)
    {
        $this->_setValue($value);
        if ($this->_min > $value) {
            $this->_error(self::NOT_GREATER);
            return false;
        }
        return true;
    }
}
