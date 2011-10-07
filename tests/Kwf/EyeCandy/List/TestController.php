<?php
class Vps_EyeCandy_List_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $view = new Vps_View();
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
