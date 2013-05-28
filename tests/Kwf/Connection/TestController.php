<?php
class Kwf_Connection_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Kwf.Test.ConnectionsError', array(
            'assetsType' => 'Kwf_Connection:Test'
        ), 'Kwf.Test.Viewport');
        $connections_counts = new Kwf_Session_Namespace('test_connection_count');
        $connections_counts->timeouts = 0;
        $connections_counts->exceptions = 0;


    }

    public function jsonTimeoutAction()
    {
        $connections_counts = new Kwf_Session_Namespace('test_connection_count');
        $connections_counts->timeouts++;
        session_write_close();
        sleep(2);
    }

    public function jsonExceptionAction()
    {
        $connections_counts = new Kwf_Session_Namespace('test_connection_count');
        $connections_counts->exceptions++;
        $this->view->exception = "exceptionError";
        $this->view->success = false;
    }

    public function getTimeoutsAction()
    {
        $connections_counts = new Kwf_Session_Namespace('test_connection_count');
        echo $connections_counts->timeouts;
        exit;

    }

    public function getExceptionsAction()
    {
        $connections_counts = new Kwf_Session_Namespace('test_connection_count');
        echo  $connections_counts->exceptions;
        exit;
    }

    public function jsonRealExceptionAction()
    {
        Zend_Registry::get('config')->debug->error->log = false;
        throw new Kwf_Exception("Exception");
    }
}
