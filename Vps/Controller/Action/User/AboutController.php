<?php
class Vps_Controller_Action_User_AboutController extends Vps_Controller_Action
{
    public function contentAction()
    {
        $this->view->application = Zend_Registry::get('config')->application->toArray();
        $this->_helper->viewRenderer->setRender('About');
    }
}
