<?php

class WebController extends Zend_Controller_Action
{
    protected $_pageCollection;
    public function indexAction()
    {
		$dao = $this->createDao();
        $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
        $pageCollection = new $pageCollectionConfig->pagecollection->type($dao);
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $this->renderPage($page);
    }

    public function ajaxAction()
    {
        echo "WebController::ajaxAction()<br />";
    }

    public function frontendEditingAction()
    {
        $componentId = $this->getRequest()->getQuery("componentId");
        if (!is_null($componentId)) {
	        $component = E3_Component_Abstract::createComponent($this->createDao(), $componentId);
	        $this->renderPage($component, true);
        }
    }

    public function filesAction()
    {
        echo "WebController::filesAction()<br />";
    }
    
    private function createDao()
    {
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'web');
        $dbConfig = $dbConfig->database->asArray();
        $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
        return new E3_Dao($db);
    }
    
    private function renderPage($page, $usePageTemplate = false)
    {
        $templateVars = $page->getTemplateVars();
        $view = new E3_View_Smarty('../application/views',
                        array('compile_dir'=>'../application/views_c'));
        $view->assign('component', $templateVars);
        if ($usePageTemplate) {
        	$body = $view->render($templateVars['template']);
        } else {
        	$body = $view->render('master/default.html');
        }

        $response = $this->getResponse();
        if ($response->canSendHeaders()) {
        	$response->setHeader('Content-Type', 'text/html');
        }
        $response->appendBody($body);
        return $response;        
    }

}
