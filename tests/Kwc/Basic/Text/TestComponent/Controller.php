<?php
class Kwc_Basic_Text_TestComponent_Controller extends Kwc_Basic_Text_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Basic_Text:Test';
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setAssetsType('Kwc_Basic_Text:Test');
    }
}
