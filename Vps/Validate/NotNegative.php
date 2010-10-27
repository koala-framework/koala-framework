<?php
class Vps_Validate_NotNegative extends Zend_Validate_Abstract
{
    const NEGATIVE = 'negative';

    public function __construct()
    {
        $this->_messageTemplates[self::NEGATIVE] = trlVps("Must not be negative");
    }

    public function isValid($value)
    {
        $this->_setValue($value);

        if ($value < 0) {
            $this->_error();
            return false;
        }
        return true;
    }

}
