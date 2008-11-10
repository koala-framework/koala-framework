<?php
class Vps_Connection_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Vps.Test.ConnectionError', array(
            'assetsType' => 'AdminTest'
        ), 'Vps.Test.Viewport');
    }
    protected function _getResourceName()
    {
        return 'vps_test';
    }
}
