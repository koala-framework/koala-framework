<?php
class Kwf_Controller_Action_Welcome_IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $location = '/';
        if ($this->getFrontController()->getRouter()->hasRoute('admin')) {
            $location = '/admin';
        }
        Kwf_Util_Redirect::redirect($location);
    }
}
