<?php
class Vps_Validate_Digits extends Zend_Validate_Digits
{
    protected $_allowEmpty;
    public function __construct($allowEmpty = false)
    {
        $this->_allowEmpty = $allowEmpty;
    }

    public function isValid($value)
    {
        $ret = parent::isValid($value);
        if ($this->_allowEmpty && !$ret) {
            $this->_errors = array();
            $this->_messages = array();
            return true;
        }
        return $ret;
    }
}
