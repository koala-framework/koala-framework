<?php
class Vps_Controller_Action_Test_ConnectionErrorController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Vps.Test.ConnectionError', array(
            'assetsType' => 'AdminTest'
        ), 'Vps.Test.Viewport');
    }
}