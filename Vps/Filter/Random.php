<?php
class Vps_Filter_Random implements Zend_Filter_Interface
{
    private $_length;
    public function __construct($length = 10)
    {
        $this->_length = $length;
    }
    public function filter($value)
    {
        return substr(uniqid('', true), 0, $this->_length);
    }
}
