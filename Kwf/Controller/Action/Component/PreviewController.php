<?php
class Kwf_Controller_Action_Component_PreviewController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->config = Kwf_Registry::get('config')->preview->toArray();
        $this->view->xtype = 'kwf.component.preview';
        $this->view->initialUrl = $this->_getParam('url');
        if (!$this->view->initialUrl) {
            $this->view->initialUrl = 'http://'.$_SERVER['HTTP_HOST'].Kwf_Config::getValue('server.basePath').'/';
        }
    }

    public function redirectAction()
    {
        if (!$this->_getParam('url')) throw new Kwf_Exception('No redirect url param found');
        if (substr($this->_getParam('url'), 0, strlen(Kwf_Config::getValue('server.basePath'))+1) !== Kwf_Config::getValue('server.basePath').'/') throw new Kwf_Exception('Invalid Url');
        header('Location: ' . $this->_getParam('url'));
        exit;
    }
}
