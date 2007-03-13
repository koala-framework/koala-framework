<?php

class WebController extends Zend_Controller_Action
{
    protected $_pageCollection;
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
            $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');

            $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'web');
            $dbConfig = $dbConfig->database->asArray();
            $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
            
            $dao = new E3_Dao($db);

            $this->_pageCollection = new $pageCollectionConfig->pagecollection->type($dao);

            $page = $this->_pageCollection->getPageByPath($this->getRequest()->getPathInfo());
            if ($page != null) {
               	$return = $page->getTemplateVars();
            }
        } catch (E3_Web_Exception $e) {
        }
        return $return;
    }

}
