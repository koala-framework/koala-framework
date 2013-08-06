<?php
class Kwf_Controller_Action_Welcome_WelcomeController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Welcome');
    }
}
