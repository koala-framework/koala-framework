<?php

class WebController extends Zend_Controller_Action
{
    protected $_pageCollection;
    public function indexAction()
    {
        echo "WebController::indexAction()<br />";
        $component = $this->getTemplateVars();
        
        $view = new E3_View_Smarty('../application/views',
                        array('compile_dir'=>'../application/views_c'));
        $view->assign('component', $component);
        $body = $view->render('master/default.html');

        $response = $this->getResponse();
        if ($response->canSendHeaders()) {
        	$response->setHeader('Content-Type', 'text/html');
        }
        $response->appendBody($body);
        return $response;        
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

        $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'web');
        $dbConfig = $dbConfig->database->asArray();
        $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
        $dao = new E3_Dao($db);

        $pageCollection = new $pageCollectionConfig->pagecollection->type($dao);
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        if ($page != null) {
           	$return = $page->getTemplateVars();
        }

        return $return;
    }

}
