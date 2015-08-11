<?php
/**
 * @package Filter
 */
class Kwf_Filter_Row_CurrentIp extends Kwf_Filter_Row_Abstract
{
    public function filter($row)
    {
        if (PHP_SAPI == 'cli') return 'cli';
        return $_SERVER['REMOTE_ADDR'];
    }
}
