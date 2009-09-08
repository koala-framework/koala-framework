<?php
class Vpc_Paragraphs_Paragraphs_Controller extends Vpc_Paragraphs_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vpc_Paragraphs:Test';
        parent::indexAction();
    }
}
