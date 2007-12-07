<?php
class Vps_Controller_Action_About extends Vps_Controller_Action
{
    public function contentAction()
    {
        $this->view->application = Zend_Registry::get('config')->application;
        $this->view->setRenderFile('About.html');
    }
}
