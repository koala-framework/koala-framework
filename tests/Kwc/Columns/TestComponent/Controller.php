<?php
class Kwc_Columns_TestComponent_Controller extends Kwc_Columns_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Columns:Test';
        parent::indexAction();
    }
}
