<?php
class Kwc_Basic_Text_TestComponent_Controller extends Kwc_Basic_Text_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwc_Basic_Text');
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setAssetsPackage(new Kwf_Assets_Package_TestPackage('Kwc_Basic_Text'));
    }
}
