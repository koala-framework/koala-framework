<?php
class Vps_Connection_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Vps.Test.ConnectionsError', array(
            'assetsType' => 'AdminTest'
        ), 'Vps.Test.Viewport');
    }
    protected function _getResourceName()
    {
        return 'vps_test';
    }

    public function jsonTimeoutAction() {
        sleep(50);
    }

    public function jsonExceptionAction() {
        throw new Vps_Exception("test Exceptions");
    }

    public function jsonSuccessAction() {
       //do nothing
    }
}
