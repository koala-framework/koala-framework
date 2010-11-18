<?php
class Vps_Test_SeleniumTestCase_TestController extends Vps_Controller_Action
{
    public function preDispatch()
    {
        //RowObserver brauchen wir hier nicht
        Vps_Component_Data_Root::setComponentClass(false);

        parent::preDispatch();
    }

    public function indexAction()
    {
        echo "index";
        exit;
    }

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
