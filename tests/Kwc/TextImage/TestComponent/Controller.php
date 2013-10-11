<?php
class Kwc_TextImage_TestComponent_Controller extends Kwc_Abstract_Composite_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_TextImage');
        parent::indexAction();
    }
}
