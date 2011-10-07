<?php
class Kwf_Validate_StringLengt extends Zend_Validate_StringLength
{
    public function __construct($min = 0, $max = null)
    {
        $this->_messageTemplates[self::TOO_SHORT] = trlKwf("'%value%' is less than %min% characters long");
        $this->_messageTemplates[self::TOO_LONG] = trlKwf("'%value%' is greater than %max% characters long");
        parent::__construct($min, $max);
    }
}
