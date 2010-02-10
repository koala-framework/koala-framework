<?php
require_once 'Vps/Exception/Abstract.php';
class Vps_Exception_NoLog extends Vps_Exception_Abstract
{
    public function getHeader()
    {
        return 'HTTP/1.1 500 Internal Server Error';
    }

    public function log() {
        return false;
    }
}
