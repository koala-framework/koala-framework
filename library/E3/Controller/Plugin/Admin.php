<?php
class E3_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{
    
    public function preDispatch(Zend_Controller_Request_Http $request)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $session = new Zend_Session_Namespace('admin');
            $identity = $auth->getIdentity();
            
            // TODO: get role of user
            $role = 'admin';
            $acl = new E3_Acl();
            
            if ($acl->isAllowed($role, 'admin')) {
                $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
                $view->assign('mode', $session->mode);
                $view->assign('_debugMemoryUsage', memory_get_usage());
                $body = $view->render('admin.html');
                $this->getResponse()->appendBody($body);
            }

            if ($acl->isAllowed($role, 'fe')) {
                // Wird nicht hier gerendert, sondern in WebController
                $request->setParam('mode', $session->mode); 
            }

        }
    }
    
}

?>
