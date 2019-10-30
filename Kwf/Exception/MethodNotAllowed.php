<?php
class Kwf_Exception_MethodNotAllowed extends Kwf_Exception
{
    public function __construct($message = "Method Not Allowed")
    {
        parent::__construct($message);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 405 Method Not Allowed';
    }

    public function getTemplate()
    {
        return 'Error405';
    }
}
