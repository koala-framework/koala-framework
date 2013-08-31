<?php
class Kwf_Controller_Action_Welcome_IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $location = $this->getRequest()->getBaseUrl().'/';
        if ($this->getFrontController()->getRouter()->hasRoute('admin')) {
            $location = $this->getRequest()->getBaseUrl().'/admin';
        }
        Kwf_Util_Redirect::redirect($location);
    }
}
