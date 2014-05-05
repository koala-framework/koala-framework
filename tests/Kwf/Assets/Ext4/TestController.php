<?php
class Kwf_Assets_Ext4_TestController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $view = new Kwf_View();
        $view->dep = new Kwf_Assets_Package(new Kwf_Assets_Ext4_TestProviderList(), 'Kwf.Assets.Ext4.TestWindow');
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Test.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function lazyLoadAction()
    {
        $view = new Kwf_View();
        $view->dep = new Kwf_Assets_Package(new Kwf_Assets_Ext4_TestProviderList(), 'Kwf.Assets.Ext4.TestLazyLoad');
        $this->getResponse()->setBody($view->render(dirname(__FILE__).'/Test.tpl'));
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
