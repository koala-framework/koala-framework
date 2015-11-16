<?php
/**
 * Replaces everything except a-z, 0-9 with -. Like alphanum VType.
 *
 * @package Filter
 */
class Kwf_Filter_Ascii implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return URLify::filter($value, 60, 'de');
    }
}
