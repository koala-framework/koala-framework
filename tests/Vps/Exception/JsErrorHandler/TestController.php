<?php
class Vps_Exception_JsErrorHandler_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vps_Exception_JsErrorHandler:Test';
        $this->view->xtype = 'panel';
        $this->view->html = '';
        for($i=1;$i<=8;$i++) {
            $this->view->html .= "<a href=\"javascript:testError$i()\">testError$i</a><br/>";
        }
    }
}
