<?php
/**
 * @package Validate
 */
class Kwf_Validate_StringLength extends Zend_Validate_StringLength
{
    public function __construct($min = 0, $max = null)
    {
        $this->_messageTemplates[self::TOO_SHORT] = trlKwfStatic("'%value%' is less than %min% characters long");
        $this->_messageTemplates[self::TOO_LONG] = trlKwfStatic("'%value%' is greater than %max% characters long");
        parent::__construct(array('min' => $min, 'max' => $max));
    }
}
