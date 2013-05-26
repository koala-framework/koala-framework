<?php
class Kwf_Controller_Action_Component_PreviewController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->config = Kwf_Registry::get('config')->preview->toArray();
        $this->view->xtype = 'kwf.component.preview';
    }

    public function redirectAction()
    {
        if (!$this->_getParam('url')) throw new Kwf_Exception('No redirect url param found');
        if (substr($this->_getParam('url'), 0, 1) !== '/') throw new Kwf_Exception('Invalid Url');
        header('Location: ' . $this->_getParam('url'));
        exit;
    }
}
