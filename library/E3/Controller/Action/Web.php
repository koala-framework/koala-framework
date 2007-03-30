<?php
class E3_Controller_Action_Web extends E3_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = E3_PageCollection_Abstract::getInstance();
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $mode = $this->getRequest()->getParam('mode');
        
        $body = $this->_renderPage($page, $mode, false);
        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->appendBody($body);
    }


    protected function _renderPage($page, $mode, $usePageTemplate)
    {
        $templateVars = $page->getTemplateVars($mode);
        $view = new E3_View_Smarty('../application/views',
                        array('compile_dir'=>'../application/views_c',
                              'debugging' => true)); //todo: ein/ausschaltbar machen
        $view->assign('component', $templateVars);
        $view->assign('mode', $mode);
        if ($usePageTemplate) {
            $body = $view->render($templateVars['template']);
        } else {
            $body = $view->render('master/default.html');
        }
        return $body;
    }

}
