<?php
class Kwf_EyeCandy_Marquee_TestController extends Kwf_Controller_Action
{
    /**
     * ist kein unit test, muss per hand aufgerufen werden
     */
    public function indexAction()
    {
        $view = new Kwf_View();
        $view->settings = array(
            'selector' => '> div',
            'scrollDelay' => 50,
            'scrollAmount' => 1,
            'scrollDirection' => 'up'
        );
        echo $view->render(dirname(__FILE__).'/Test.tpl');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
