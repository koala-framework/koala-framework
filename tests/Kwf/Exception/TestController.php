<?php
class Vps_Exception_TestController extends Vps_Controller_Action
{
    public function noExceptionAction()
    {
        echo 'OK';
        exit;
    }

    public function accessDeniedAction()
    {
        throw new Vps_Exception_AccessDenied();
    }

    public function notFoundAction()
    {
        throw new Vps_Exception_NotFound();
    }

    public function clientAction()
    {
        throw new Vps_Exception_Client("client exception");
    }

    public function exceptionAction()
    {
        throw new Vps_Exception("client exception");
    }

    public function exceptionOtherAction()
    {
        throw new Exception();
    }
}
