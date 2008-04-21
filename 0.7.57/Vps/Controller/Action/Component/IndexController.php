<?php
class Vps_Controller_Action_Component_IndexController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->ext('Welcome');
    }
}
