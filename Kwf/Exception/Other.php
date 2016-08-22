<?php
class Kwf_Exception_Other extends Kwf_Exception
{
    private $_exception;

    public function __construct($exception)
    {
        $this->_exception = $exception;
    }

    public function getException()
    {
        return $this->_exception;
    }
}
