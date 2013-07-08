<?php
/**
 * @package Filter
 */
class Kwf_Filter_Random implements Zend_Filter_Interface
{
    private $_length;
    public function __construct($length = 10)
    {
        $this->_length = $length;
    }

    public function filter($value)
    {
        if ($value) return $value;
        return $this->_generateRandStr($this->_length);
    }

    private function _generateRandStr($length)
    {
        $letters = '23456789qwertyupasdfghjkzxcvbnm';

        $s = '';
        $lettersLength = strlen($letters)-1;
        for($i = 0 ; $i < $length ; $i++) {
            $s .= $letters[rand(0,$lettersLength)];
        }
        return $s;
    }
}
