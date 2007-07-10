<?php
class Vps_Controller_Action_Component_Web extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        if (!$page) {
            throw new Vps_Controller_Action_Web_Exception('Page not found for path ' . $this->getRequest()->getPathInfo());
        }
        
        $mode = $this->getRequest()->getParam('mode');
        $templateVars = $page->getTemplateVars($mode);

        $this->view->url = $this->getRequest()->getPathInfo();
        $this->view->component = $templateVars;
        $this->view->title = $pageCollection->getTitle($page);
        $this->view->mode = $mode;
    }

    public function postDispatch()
    {
        // Menu
        $role = $this->_getUserRole();
        $showMenu = substr($_SERVER['HTTP_HOST'], 0, 4) == 'cms.' || $role != 'guest';

        if ($showMenu) {
            $config['url'] = $this->getRequest()->getPathInfo();
            $config['controllerUrl'] = '/admin/menu/';
            //$config['_debugMemoryUsage'] = memory_get_usage();
            $renderTo = 'Ext.DomHelper.insertFirst(document.body, \'<div \/>\', true)';
            $this->view->ext('Vps.Menu.Index', $config, $renderTo);
        }
    }

}
