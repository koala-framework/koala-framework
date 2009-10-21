<?php
class Vps_Filter_Row_Random extends Vps_Filter_Row_Abstract
{
    private $_length;
    public function __construct($length = 10)
    {
        $this->_length = $length;
    }
    public function filter($row)
    {
        if (!$row->{$this->_field}) {
            return $this->_generateRandStr($this->_length);
        } else {
            return $row->{$this->_field};
        }
    }

    private function _generateRandStr($length)
    {
        $letters = '1234567890qwertyuiopasdfghjklzxcvbnm';

        $s = '';
        $lettersLength = strlen($letters)-1;
        for($i = 0 ; $i < $length ; $i++) {
            $s .= $letters[rand(0,$lettersLength)];
        }
        return $s;
    }
}
