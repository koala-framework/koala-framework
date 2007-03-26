<?php
class E3_Controller_Plugin_Fe extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Http $request)
    {
        if ($request->getControllerName() == 'web' && $request->getActionName() == 'fe') {

            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
                $acl = new E3_Acl();
                
                // TODO: get role of user
                $role = 'admin';
                if ($acl->isAllowed($role, 'fe')) {
                    $pathInfo = $request->getPathInfo();
                    $pathInfo = str_replace('/edit', '', $pathInfo);
                    $request->setPathInfo($pathInfo); 
                } else {
                    echo "You are not allowed to edit this page.";
                }
            } else {
                $request->setControllerName('admin');
                $request->setActionName('login');
            }
        }
        
    }
}

?>
