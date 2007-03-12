<?php
Zend::loadClass('E3_Web');

class WebController extends Zend_Controller_Action
{
    public function indexAction()
    {
        echo "WebController::indexAction()<br />";
        p($this->getTemplateVars());
    }

    public function ajaxAction()
    {
        echo "WebController::ajaxAction()<br />";
        p($this->getTemplateVars());
    }

    public function filesAction()
    {
        echo "WebController::filesAction()<br />";
    }
    
    private function getTemplateVars()
    {
        $return = array();
        try {
            $web = E3_Web::getInstance();
            $page = $web->getPageByPath($this->getRequest()->getPathInfo());
            if ($page != null) {
               	$return = $page->getTemplateVars();
            }
        } catch (E3_Web_Exception $e) {
        }
        return $return;    	
    }

}
?>