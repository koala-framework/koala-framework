<?php
class Kwc_Columns_Basic_TestComponent_Controller extends Kwc_Columns_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Columns_Basic:Test';
        parent::indexAction();
    }
}
