<?php
abstract class Kwc_Errors_Abstract_Component extends Kwc_Abstract
{
    protected $_exception;
    public function setException($e)
    {
        $this->_exception = $e;
    }

    public function getException()
    {
        return $this->_exception;
    }
}
