<?php
class Kwf_Exception_BadRequest extends Kwf_Exception
{
    public function __construct($message = "Bad Request")
    {
        parent::__construct($message);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 400 Bad Request';
    }

    public function getTemplate()
    {
        return 'Error400';
    }
}
