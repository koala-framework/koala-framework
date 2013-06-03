<?php
class Kwf_EyeCandy_Tabs_TestController extends Kwf_Controller_Action
{
    /**
     * no unit test, has to be tested manually
     * /kwf/test/kwf_eye-candy_tabs_test
     *
     * Important points to check:
     *  1. fast switching between tabs.
     *  2. Openening hidden region and check if tabs are resizing correctly,
     *     moving content down
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
