<?php
class Kwc_Trl_News_News_Trl_Controller extends Kwc_News_Directory_Trl_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Trl_News:Test';
        parent::indexAction();
    }
}
