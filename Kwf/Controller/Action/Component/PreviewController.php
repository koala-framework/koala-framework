<?php
class Kwf_Controller_Action_Component_PreviewController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->xtype = 'kwf.component.preview';
    }

    public function redirectAction()
    {
        if (!$this->_getParam('url')) throw new Kwf_Exception('No redirect url param found');
        header('Location: ' . urldecode($this->_getParam('url')));
        exit;
    }
}
