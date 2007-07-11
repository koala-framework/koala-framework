<?php
class Vps_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Seite bearbeiten-Button
        if ($this->getRequest()->getModuleName() != 'admin') {
            $acl = Zend_Registry::get('acl');

            $pageId = $this->getRequest()->getParam('pageId');
            $url = $this->getRequest()->getParam('url');
            if ($pageId != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->getPageById($pageId);
                $path = $pageCollection->getUrl($page);
                $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite betrachten', $path));
                $acl->allow('admin', 'page');
            } else if ($url != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->getPageByPath($url);
                if ($page) {
                    $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite bearbeiten', '/admin/page?id=' . $page->getId()));
                    $acl->allow('admin', 'page');
                }
            } else {
                $pageId = 0;
            }
    
            Zend_Registry::set('acl', $acl);
        }


        if (substr($request->getActionName(), 0, 4) == 'ajax') {
            return;
        }

        /*
        if ($this->_isAllowed('fe')) {
            $session = new Zend_Session_Namespace('admin');
            $request->setParam('mode', $session->mode);
        }

        if ($request->getControllerName() == 'fe') {
            return;
        }
        */
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        /*
        // Frontend Editing
        $session = new Zend_Session_Namespace('admin');
        if ($this->_isAllowed('fe') && $session->mode == 'fe') {
            $view = new Vps_View_Smarty(VPS_PATH . '/views');
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
            $componentsInfo = array();
            $components = array();
            if ($page != null) {
                $componentsInfo = $page->getComponentInfo();
                foreach ($componentsInfo as $key => $component) {
                    $filename = str_replace('_', '/', $component) . '.js';
                    if (!is_file('../library/' . $filename)) {
                        unset($componentsInfo[$key]);
                    }
                }
                $view->assign('componentsInfo', $componentsInfo);
                $view->assign('currentPageId', $page->getId());
                $body = $view->render('fe.html');
                $this->getResponse()->appendBody($body);
            }
        }
        */
    }

}

?>
