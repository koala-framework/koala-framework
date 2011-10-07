<?php
class Vps_Media_Headline_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->setScriptPath(dirname(__FILE__));
        $this->_helper->viewRenderer->setRender('test');
    }
}
