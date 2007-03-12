<?php
require_once 'Zend/Controller/Router/Rewrite.php';
require_once 'E3/Web.php';

class E3_Controller_Router extends Zend_Controller_Router_Rewrite
{
	public function route(Zend_Controller_Request_Abstract $request)
	{
        $this->removeDefaultRoutes();
        $parentRequest = parent::route($request);
        
        try {
            $this->getCurrentRoute();
            $reques = $parentRequest;
        } catch (Zend_Controller_Router_Exception $e) {
            ////$this->getFrontController()->setRequest('E3_Controller_Request');
            //$request = $this->getFrontController()->getRequest();
            $web = E3_Web::getInstance();
            $page = $web->getPageByPath($request->getPathInfo());
            if ($page != null) {
                $request->setControllerName("web");
                $request->setActionName("index");
            }
        }
        return $request;
	}
}
?>
