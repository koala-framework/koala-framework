<?php
class E3_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{

    private function _isAllowed($resource)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();

            // TODO: get role of user
            $role = 'admin';
            $acl = new E3_Acl();

            return $acl->isAllowed($role, $resource);
        }
        return false;
    }

    public function preDispatch(Zend_Controller_Request_Http $request)
    {
        if ($this->_isAllowed('fe')) {
            $session = new Zend_Session_Namespace('admin');
            $request->setParam('mode', $session->mode);
        }

        if (substr($request->getPathInfo(), 0, 6) == '/admin' && $request->getActionName() != 'login' && !$this->_isAllowed('admin')) {
            header('Location: /admin/login'); // hab ich mir dem Response-Objekt nicht geschafft
            die();
        }

        if ($request->getControllerName() == 'fe' || strpos($request->getActionName(), 'ajax') !== false) {
            return false;
        }

        if ($this->_isAllowed('admin')) {
            $session = new Zend_Session_Namespace('admin');
            $mode = $session->mode;
            $path = $this->getRequest()->getParam('path');
            if ($path == null) {
                $path = $this->getRequest()->getPathInfo();
            }

            $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
            $view->assign('mode', $mode);
            $view->assign('path', $path);
            $view->assign('_debugMemoryUsage', memory_get_usage());
            $body = $view->render('admin.html');
            $this->getResponse()->appendBody($body);
        }

    }

    public function postDispatch(Zend_Controller_Request_Http $request)
    {
        if ($this->_isAllowed('fe')) {
            $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
            $pageCollection = E3_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
            $componentsInfo = array();
            $components = array();
            if ($page != null) {
                foreach ($page->getComponentInfo() as $key => $component) {
                    $filename = str_replace('_', '/', $component) . '.js';
                    if (is_file('../library/' . $filename)) {
                        $componentsInfo[$key] = str_replace('_', '.', $component);
                        $components[] = $filename;
                    }
                }
                $view->assign('componentsInfo', $componentsInfo);
                $view->assign('components', array_unique($components));
                $body = $view->render('fe.html');
                $this->getResponse()->appendBody($body);
            }
        }
    }

}

?>
