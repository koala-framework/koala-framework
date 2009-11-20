<?php
class Vpc_Columns_TestComponent_Controller extends Vpc_Columns_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Columns:Test';
        parent::indexAction();
    }
}
