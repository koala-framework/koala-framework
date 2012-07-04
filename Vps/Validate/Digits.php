<?php
class Vps_Validate_Digits extends Zend_Validate_Digits
{
    public function __construct()
    {
        $this->_allowEmpty = $allowEmpty;
    }

    public function isValid($value)
    {
        $ret = parent::isValid($value);
        return $ret;
    }
}
