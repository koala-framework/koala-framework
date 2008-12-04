<?php
class Vps_Exception_Client extends Vps_Exception_NoMail
{
    public function getHeader()
    {
        return 'HTTP/1.1 200 OK';
    }

    public function getTemplate()
    {
        return 'ErrorClient';
    }
}
