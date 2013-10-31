<?php
class Kwc_FormDynamic_Basic_Form_Paragraphs_Controller extends Kwc_Paragraphs_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_FormDynamic_Basic');
    }
}
