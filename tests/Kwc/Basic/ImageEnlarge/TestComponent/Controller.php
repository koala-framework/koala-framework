<?php
class Vpc_Basic_ImageEnlarge_TestComponent_Controller extends Vpc_Abstract_Composite_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Basic_ImageEnlarge:Test';
    }
}
