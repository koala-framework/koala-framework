<?php
class Vps_Exception_Other extends Vps_Exception
{
    private $_exception;

    public function __construct(Exception $exception)
    {
        $this->_exception = $exception;
    }

    public function getException()
    {
        return $this->_exception;
    }
}
