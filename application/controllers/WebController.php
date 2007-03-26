<?php

class WebController extends Zend_Controller_Action
{
    protected $_pageCollection;
    public function indexAction()
    {
        $dao = $this->createDao();
        $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
        $pageCollection = new $pageCollectionConfig->pagecollection->type($dao);
        $pageCollection->setAddDecorator($pageCollectionConfig->pagecollection->addDecorator);
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $this->renderPage($page);
        return $page;
    }

    public function feAction()
    {
        $page = $this->indexAction();
        $params = $this->getRequest()->getParams();
        $view = new E3_View_Smarty('../library/E3', array('compile_dir'=>'../application/views_c'));
        $componentsInfo = array();
        $components = array();
        foreach ($page->getComponentInfo() as $key => $component) {
            $filename = str_replace('_', '/', $component) . '.js';
            if (is_file('../library/' . $filename)) {
                $componentsInfo[$key] = str_replace('_', '.', $component);
                $components[] = $filename;
            }
        }
        $view->assign('componentsInfo', $componentsInfo);
        $view->assign('components', array_unique($components));
        $body = $view->render('fe.html');
        
        $this->getResponse()->appendBody($body);
    }

    public function ajaxAction()
    {
        echo "WebController::ajaxAction()<br />";
    }

    public function frontendEditingAction()
    {
        $id = $this->getRequest()->getQuery("componentId");
        if (!is_null($id)) {
            $dao = $this->createDao();
            $className = str_replace(".", "_", $this->getRequest()->getQuery("componentClass"));
            preg_match('#^([^_\\|]*)_?([^_\\|]*)\\|?([^_\\|]*)$#', $id, $keys);
            $component = new $className($dao, $keys[1], $keys[2], $keys[3]);
            if ($this->getRequest()->getQuery("save")) {
                $component->saveFrontendEditing();
            }
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
    }

}
