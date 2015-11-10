<?php
/**
 * Ersetzt alles außer a-z, 0-9 - durch _. So wie alphanum VType vom Ext2.
 *
 * @package Filter
 */
class Kwf_Filter_Ascii implements Zend_Filter_Interface
{
    public function filter($value)
    {
        URLify::$remove_list = array();
        $value = URLify::filter($value, 60, 'de');
        return $value;
    }
}
