<?php
class Kwf_Js_OnContentReady_TestController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View();
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Test.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function page1Action()
    {
        $view = new Kwf_View();
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Page1.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function page2Action()
    {
        $view = new Kwf_View();
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Page2.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
