<?php
class Kwf_Exception_TestController extends Kwf_Controller_Action
{
    public function noExceptionAction()
    {
        echo 'OK';
        exit;
    }

    public function accessDeniedAction()
    {
        throw new Kwf_Exception_AccessDenied();
    }

    public function notFoundAction()
    {
        throw new Kwf_Exception_NotFound();
    }

    public function clientAction()
    {
        throw new Kwf_Exception_Client("client exception");
    }

    public function exceptionAction()
    {
        throw new Kwf_Exception("client exception");
    }

    public function exceptionOtherAction()
    {
        throw new Exception();
    }
}
