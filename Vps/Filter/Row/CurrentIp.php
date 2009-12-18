<?php
class Vps_Filter_Row_CurrentIp extends Vps_Filter_Row_Abstract
{
    public function filter($row)
    {
        if (php_sapi_name() == 'cli') return 'cli';
        return $_SERVER['REMOTE_ADDR'];
    }
}
