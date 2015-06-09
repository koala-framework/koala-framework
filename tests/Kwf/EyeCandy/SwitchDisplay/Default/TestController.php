<?php
class Kwf_EyeCandy_SwitchDisplay_Default_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View();
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Test.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
