<?php
class Vps_Connection_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Vps.Test.ConnectionsError', array(
            'assetsType' => 'AdminTest'
        ), 'Vps.Test.Viewport');
        $connections_counts = new Zend_Session_Namespace('test_connection_count');
        $connections_counts->timeouts = 0;
        $connections_counts->exceptions = 0;


    }

    public function jsonTimeoutAction()
    {
        $connections_counts = new Zend_Session_Namespace('test_connection_count');
        $connections_counts->timeouts++;
        session_write_close();
        sleep(2);
    }

    public function jsonExceptionAction()
    {
        $connections_counts = new Zend_Session_Namespace('test_connection_count');
        $connections_counts->exceptions++;
        $this->view->exception = "exceptionError";
        $this->view->success = false;
    }

    public function jsonSuccessAction()
    {
       //do nothing
    }

    public function getTimeoutsAction()
    {
        $connections_counts = new Zend_Session_Namespace('test_connection_count');
        echo $connections_counts->timeouts;
        exit;

    }

    public function getExceptionsAction()
    {
        $connections_counts = new Zend_Session_Namespace('test_connection_count');
        echo  $connections_counts->exceptions;
        exit;
    }

    public function jsonRealExceptionAction()
    {
        Zend_Registry::get('config')->debug->errormail = false;
        throw new Vps_Exception("Exception");
    }
}
