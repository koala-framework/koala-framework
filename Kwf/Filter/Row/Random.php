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
            $filter = new Vps_Filter_Random($this->_length);
            return $filter->filter('');
        } else {
            return $row->{$this->_field};
        }
    }

}
