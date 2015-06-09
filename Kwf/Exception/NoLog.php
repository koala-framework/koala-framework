<?php
class Kwf_Exception_NoLog extends Kwf_Exception_Abstract
{
    public function getHeader()
    {
        return 'HTTP/1.1 500 Internal Server Error';
    }

    public function log() {
        return false;
    }
}
