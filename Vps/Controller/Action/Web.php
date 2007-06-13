<?php
class Vps_Controller_Action_Web extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $mode = $this->getRequest()->getParam('mode');
        $templateVars = $page->getTemplateVars($mode);

        $this->view->component = $templateVars;
        $this->view->title = $pageCollection->getTitle($page);
        $this->view->mode = $mode;
    }

}
