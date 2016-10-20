<?php
class Kwf_Exception_Conflict extends Kwf_Exception
{
    public function __construct($message = "Conflict")
    {
        parent::__construct($message, 409);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 409 Conflict';
    }
}
