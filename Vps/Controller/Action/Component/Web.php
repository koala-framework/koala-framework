<?php
class Vps_Controller_Action_Component_Web extends Vps_Controller_Action
{
    public function preDispatch()
    {
        // Seite bearbeiten-Button
        /*
        if ($this->getRequest()->getModuleName() != 'admin') {
            $acl = Zend_Registry::get('acl');

            $pageId = $this->getRequest()->getParam('pageId');
            $url = $this->getRequest()->getParam('url');
            if ($pageId != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->findPage($pageId);
                $path = $pageCollection->getUrl($page);
                $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite betrachten', $path));
                $acl->allow('admin', 'page');
            } else if ($url != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->findPageByPath($url);
                if ($page) {
                    $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite bearbeiten', '/admin/page?id=' . $page->getId()));
                    $acl->allow('admin', 'page');
                }
            } else {
                $pageId = 0;
            }

            Zend_Registry::set('acl', $acl);
        }
*/

    }

    public function indexAction()
    {
        $logger = new Zend_Log_Writer_Mock();
        $log = new Zend_Log($logger);
        $log->addPriority('createPage', 10);
        $log->addPriority('createComponent', 11);
        Zend_Registry::set('infolog', $log);

        $benchmark = Vps_Benchmark::getInstance();
        $benchmark->startSequence('Seitenbaum');

        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $page = $pageCollection->findPageByPath($this->getRequest()->getPathInfo());
        if (!$page) {
            throw new Vps_Controller_Action_Web_Exception('Page not found for path ' . $this->getRequest()->getPathInfo());
        }

        $templateVars = $page->getTemplateVars();

        $this->view->url = $this->getRequest()->getPathInfo();
        $this->view->component = $templateVars;
        $this->view->title = $pageCollection->getTitle($page);
        $this->view->mode = ''; // Für Smarty-Plugin

        $benchmark->stopSequence('Seitenbaum');
        $result = $benchmark->getResults();
        $this->view->time = sprintf("%01.2f", $result['Seitenbaum']['duration']);
    }

    public function postDispatch()
    {
        $role = $this->_getUserRole();
        $acl = $this->_getAcl();

        $config = array();
        $config['menu'] = false;
        $config['fe'] = false;

        /*
        if ($acl->isAllowed($role, 'pages')) {
            //$config['menu'] = true;
        }

        if ($acl->isAllowed($role, 'fe')) {
            $session = new Zend_Session_Namespace('admin');
            if ($session->mode == 'fe' || $this->getRequest()->getParam('fe')) {
                $this->view->mode = 'fe';
                $config['fe'] = true;
            }
        }
        */

        if ($config['menu'] || $config['fe']) {
            $this->view->ext('Vps.Component.Frontend.Index', $config);
        }
    }

}
