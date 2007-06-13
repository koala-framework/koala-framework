<?php
class Vps_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{

    private function _isAllowed($resource)
    {
        /*
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            // TODO: get role of user
            $role = 'admin';
            $acl = new Vps_Acl();

            return $acl->isAllowed($role, $resource);
        }
        return false;
        */
        return true;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $acl = new Vps_Acl();
        
        // Roles
        $acl->addRole(new Vps_Acl_Role('guest'));
        $acl->addRole(new Vps_Acl_Role('admin'));
        
        // Resources
        $acl->add(new Zend_Acl_Resource('web'));
        $acl->add(new Vps_Acl_Resource('admin', 'Admin'));
            $acl->add(new Vps_Acl_Resource('admin_pages', 'Seitenbaum', '/admin/pages'), 'admin');
            $acl->add(new Zend_Acl_Resource('admin_page'), 'admin');
            $acl->add(new Zend_Acl_Resource('admin_component'), 'admin');
            $acl->add(new Zend_Acl_Resource('admin_menu'), 'admin');
        
        // Berechtigungen
        $acl->allow('admin', 'admin');
        $acl->allow('admin', 'web');
        $acl->allow('guest', 'web');

        // Seite bearbeiten-Button
        $pageId = $this->getRequest()->getParam('pageId');
        $url = $this->getRequest()->getParam('url');
        if ($pageId != '') {
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageById($pageId);
            $path = $pageCollection->getPath($page);
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
        $userNamespace = new Zend_Session_Namespace('User');
//        return $userNamespace->role;

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
