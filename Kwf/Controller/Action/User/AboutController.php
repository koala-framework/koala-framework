<?php
class Kwf_Controller_Action_User_AboutController extends Kwf_Controller_Action
{
    public function contentAction()
    {
        $this->view->application = Zend_Registry::get('config')->application->toArray();
        $this->_helper->viewRenderer->setRender('About');
    }
}
