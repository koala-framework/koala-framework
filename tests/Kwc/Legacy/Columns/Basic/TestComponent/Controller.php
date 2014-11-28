<?php
class Kwc_Legacy_Columns_Basic_TestComponent_Controller extends Kwc_Legacy_Columns_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_Legacy_Columns_Basic');
        parent::indexAction();
    }
}
