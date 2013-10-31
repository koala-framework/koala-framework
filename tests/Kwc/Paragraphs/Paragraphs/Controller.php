<?php
class Kwc_Paragraphs_Paragraphs_Controller extends Kwc_Paragraphs_Controller
{
    public function indexAction()
    {
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_Paragraphs');
        parent::indexAction();
    }
}
