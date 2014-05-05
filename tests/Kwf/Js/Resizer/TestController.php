<?php
class Kwf_Js_Resizer_TestController extends Kwf_Controller_Action
{
    /**
     * no unit test. Has to be invoked manually
     * /kwf/test/kwf_js_resizer_test
     */
    public function indexAction()
    {
        $view = new Kwf_View();
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Test.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
