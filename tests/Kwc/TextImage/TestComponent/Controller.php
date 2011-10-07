<?php
class Vpc_TextImage_TestComponent_Controller extends Vpc_Abstract_Composite_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_TextImage:Test';
        parent::indexAction();
    }
}
