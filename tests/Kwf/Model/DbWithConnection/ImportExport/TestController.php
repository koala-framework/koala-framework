<?php
class Vps_Model_DbWithConnection_ImportExport_TestController extends Vps_Controller_Action
{
    public function exportAction()
    {
        Zend_Registry::set('db', Vps_Test::getTestDb());

        $server = new Vps_Srpc_Server();
        $server->setClass('Vps_Model_DbWithConnection_ImportExport_Handler');
        $server->handle();
        exit();
    }

    public function importAction()
    {
        Zend_Registry::set('db', Vps_Test::getTestDb());

        $server = new Vps_Srpc_Server();
        $server->setClass('Vps_Model_DbWithConnection_ImportExport_Handler');
        $server->handle();
        exit();
    }
}
