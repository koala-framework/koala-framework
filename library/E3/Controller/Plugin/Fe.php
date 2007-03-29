<?php
class E3_Controller_Plugin_Fe extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Http $request)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $acl = new E3_Acl();
            
            // TODO: get role of user
            $role = 'admin';
            if ($acl->isAllowed($role, 'fe')) {
                $this->_renderAdminPanel();
            }
        }
    }
    
    private function _renderAdminPanel()
    {
        $adminSession = new Zend_Session_Namespace('admin');
        
        $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
        $view->assign('mode', $adminSession->mode);
        $view->assign('_debugMemoryUsage', memory_get_usage());
        $body = $view->render('admin.html');
        $this->getResponse()->appendBody($body);
        
        if ($adminSession->mode == 'fe') {
            $this->_renderFe();
        }
        
    }
    
    private function _renderFe()
    {
        if (Zend_Registry::isRegistered('page')) {
            $page = Zend_Registry::get('page');
            $params = $this->getRequest()->getParams();
            $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
            $componentsInfo = array();
            $components = array();
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

?>
