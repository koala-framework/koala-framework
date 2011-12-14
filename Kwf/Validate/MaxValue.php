<?php
/**
 * @package Validate
 */
class Kwf_Validate_MaxValue extends Zend_Validate_LessThan
{
    public function __construct($max)
    {
        $this->_messageTemplates[self::NOT_LESS] = trlKwf("'%value%' must be less or equal than '%max%'");
        parent::__construct($max);
    }

    public function isValid($value)
    {
        $this->_setValue($value);
        if ($this->_max < $value) {
            $this->_error(self::NOT_LESS);
            return false;
        }
        return true;
    }
}
