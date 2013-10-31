<?php
class Kwc_Columns_Basic_TestComponent_Controller extends Kwc_Columns_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_Columns_Basic');
        parent::indexAction();
    }
}
