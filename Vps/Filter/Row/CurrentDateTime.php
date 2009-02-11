<?php
class Vps_Filter_Row_CurrentDateTime extends Vps_Filter_Row_Abstract
{
    protected $_dateFormat;

    public function __construct($dateFormat = 'Y-m-d H:i:s')
    {
        $this->_dateFormat = $dateFormat;
    }

    public function filter($row)
    {
        return date($this->_dateFormat);
    }
}
