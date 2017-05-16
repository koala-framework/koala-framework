<?php
class Kwf_Controller_Action_User_AboutController extends Kwf_Controller_Action
{
    public function contentAction()
    {
        $this->view->application = Zend_Registry::get('config')->application->toArray();
        if (Kwf_Registry::get('userModel')->getAuthedUserRole() != 'admin') {
            $this->view->application['kwf']['version'] = null;
        }
        $this->_helper->viewRenderer->setRender('About');
    }
}
