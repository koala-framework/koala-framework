<?php
class Vpc_Trl_News_News_Trl_Controller extends Vpc_News_Directory_Trl_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Trl_News:Test';
        parent::indexAction();
    }
}
