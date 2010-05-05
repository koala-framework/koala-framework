<?php
class Vpc_Menu_Menu_Controller extends Vpc_Menu_Abstract_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Menu:Test';
    }
}
