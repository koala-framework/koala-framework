<?php
class Vps_Test_SeleniumTestCase_TestController extends Vps_Controller_Action
{
    public function fatalErrorAction()
    {
        Zend_Registry::get('config')->debug->error->log = false;
        $x = null;
        $x->y();
    }

    public function exceptionAction()
    {
        Zend_Registry::get('config')->debug->error->log = false;
        throw new Vps_Exception('my exception');
    }
}
