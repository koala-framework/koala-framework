<?php
class Vps_Exception_AccessDenied extends Vps_Exception_NoMail
{
    public function getHeader()
    {
        return 'HTTP/1.1 401 Access Denied';
    }

    public function getTemplate()
    {
        return 'Error401';
    }
}
