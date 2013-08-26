<?php
class Kwf_Js_OnReadyPriority_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View();
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
