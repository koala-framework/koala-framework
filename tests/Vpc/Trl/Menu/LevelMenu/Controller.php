<?php
class Vpc_Trl_Menu_LevelMenu_Controller extends Vpc_Menu_Abstract_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Trl_Menu:Test';
    }
}
