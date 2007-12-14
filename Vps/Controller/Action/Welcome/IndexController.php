<?php
class Vps_Controller_Action_Welcome_IndexController  extends Vps_Controller_Action
{
    public function indexAction()
    {
        $location = '/';
        if ($this->getFrontController() instanceof Vps_Controller_Front_Component) {
            $location = '/admin';
        }
        header('Location: ' . $location);
        die();
    }
}
