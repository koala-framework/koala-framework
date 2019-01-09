<?php
/**
 * Replaces everything except a-z, 0-9 with -. Like alphanum VType.
 *
 * @package Filter
 */
class Kwf_Filter_Ascii implements Zend_Filter_Interface
{
    /**
     * @var int
     */
    private $_length;

    public function __construct($length = 60)
    {
        $this->_length = $length;
    }

    public function filter($value)
    {
        URLify::$remove_list = array();
        return URLify::filter($value, $this->_length, 'de');
    }
}
