<?php
class Kwc_Basic_ImageEnlarge_TestComponent_Controller extends Kwc_Abstract_Composite_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Basic_ImageEnlarge:Test';
    }
}
