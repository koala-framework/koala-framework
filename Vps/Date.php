<?php
class Vps_Date
{
    //TODO: DateTime verwenden, das unterstützt datum vor 1970 und nach 2038
    protected $_timestamp;

    public function __construct($date)
    {
        if (is_int($date)) {
            $this->_timestamp = $date;
        } else {
            $this->_timestamp = strtotime($date);
        }
        if (!$this->_timestamp) {
            throw new Vps_Exception('Invalid date');
        }
    }

    public function format($format = 'Y-m-d')
    {
        return date($format, $this->_timestamp);
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }
}
