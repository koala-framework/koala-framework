<?php
/**
 * Ersetzt alles außer a-z, 0-9 - durch _. So wie alphanum VType vom Ext2.
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
        $value = URLify::filter($value, $this->_length, 'de');
        $value = str_replace('-', '_', $value);
        return $value;
    }
}
