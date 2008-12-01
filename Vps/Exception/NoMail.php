<?php
class Vps_Exception_NoMail extends Exception
{
    public function getHeader()
    {
        return 'HTTP/1.1 500 Internal Server Error';
    }

    public function getTemplate()
    {
        return 'Error';
    }

    public static function isDebug()
    {
        return Zend_Registry::get('config')->debug->errormail == '';
    }

    public function getException()
    {
        return $this;
    }
}
