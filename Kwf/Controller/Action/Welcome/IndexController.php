<?php
class Kwf_Controller_Action_Welcome_IndexController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $location = '/';
        if ($this->getFrontController() instanceof Kwf_Controller_Front_Component) {
            $location = '/admin';
        }
        header('Location: ' . $location);
        die();
    }
}
