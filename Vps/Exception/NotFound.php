<?php
class Vps_Exception_NotFound extends Vps_ExceptionNoMail {
    public function getHeader()
    {
        return 'HTTP/1.1 404 Not Found';
    }

    public function getTemplate()
    {
        return 'Error404';
    }
}