<?php
class Kwf_Model_DbWithConnection_ImportExport_TestController extends Kwf_Controller_Action
{
    public function exportAction()
    {
        Zend_Registry::set('db', Kwf_Test::getTestDb());

        $server = new Kwf_Srpc_Server();
        $server->setClass('Kwf_Model_DbWithConnection_ImportExport_Handler');
        $server->handle();
        exit();
    }

    public function importAction()
    {
        Zend_Registry::set('db', Kwf_Test::getTestDb());

        $server = new Kwf_Srpc_Server();
        $server->setClass('Kwf_Model_DbWithConnection_ImportExport_Handler');
        $server->handle();
        exit();
    }
}
