<?php
class Kwf_Js_HistoryState_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View();
        $view->result = 'index';
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function subAction()
    {
        $view = new Kwf_View();
        $view->result = 'sub';
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
