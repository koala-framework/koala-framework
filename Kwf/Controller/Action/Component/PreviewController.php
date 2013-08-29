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
        Kwf_Util_Redirect::redirect($this->_getParam('url'));
    }
}
