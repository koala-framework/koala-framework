<?php
class E3_Controller_Action_Pages extends E3_Controller_Action
{
    public function actionAction()
    {
        $pageCollection = E3_PageCollection_Abstract::getInstance();
        $path = $this->getRequest()->getParam('path');
        $hierarchy = $pageCollection->getFlatHierarchy($path);

        $view = new E3_View_Smarty('../library/E3/Controller/Action',
                        array('compile_dir'=>'../application/views_c',
                              'debugging' => true)); //todo: ein/ausschaltbar machen
        $view->assign('rootId', $pageCollection->getRootPage()->getId());
        $view->assign('hierarchy', $hierarchy);
        $body = $view->render("Pages.html");

        $response = $this->getResponse();
        $response->appendBody($body);
    }
    
}
