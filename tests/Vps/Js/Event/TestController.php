<?php
class Vps_Js_Event_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $view = new Vps_View();
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
