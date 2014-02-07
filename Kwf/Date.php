<?php
class Kwf_Date
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
            throw new Kwf_Exception('Invalid date');
        }
    }

    public static function create($date)
    {
        return new self($date);
    }

    public function format($format = 'Y-m-d', $language = null)
    {
        $needsZendDate = !!array_intersect(array('D', 'l', 'S', 'F', 'M', 'e'), str_split($format));
        if (!$needsZendDate) {
            return date($format, $this->_timestamp);
        } else {
            $date = new Zend_Date($this->_timestamp, Zend_Date::TIMESTAMP);
            $date->setOptions(array(
                'format_type' => 'php'
            ));
            $date->setLocale(Kwf_Trl::getInstance()->trlcKwf('locale', 'C', array(), $language));
            return $date->toString($format);
        }
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }
}
