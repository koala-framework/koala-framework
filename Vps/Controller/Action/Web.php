<?php
class Vps_Controller_Action_Web extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $mode = $this->getRequest()->getParam('mode');
        
        $templateVars = $page->getTemplateVars($mode);
        $view = new Vps_View_Smarty();
        $view->assign('component', $templateVars);
        $view->assign('title', $pageCollection->getTitle($page));
        $view->assign('mode', $mode);
        $body = $view->render('master/default.html');
        $this->getResponse()->appendBody($body);
    }


}
