<?php
class E3_Controller_Action_Web extends E3_Controller_Action
{
    public function indexAction()
    {
        $mode = $this->getRequest()->getParam('mode');
        $dao = $this->createDao();
        $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
        $pageCollection = new $pageCollectionConfig->pagecollection->type($dao);
        $pageCollection->setAddDecorator($pageCollectionConfig->pagecollection->addDecorator);
        $page = $pageCollection->getPageByPath($this->getRequest()->getPathInfo());
        $this->_renderPage($page, $mode, false);
        $this->_renderFe($page, $mode);
    }

    public function feSaveAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $component->saveFrontendEditing();
            $this->_renderPage($component, 'fe', true);
        }
    }
    
    public function feCancelAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $this->_renderPage($component, 'fe', true);
        }
    }
    
    public function feEditAction()
    {
        $component = $this->_createComponent();
        if (!is_null($component)) {
            $this->_renderPage($component, 'edit', true);
        }
    }

    private function _createComponent()
    {
        $id = $this->getRequest()->getQuery('componentId');
        if (is_null($id)) return null;
        $dao = $this->createDao();
        $className = str_replace(".", "_", $this->getRequest()->getQuery('componentClass'));
        preg_match('#^([^_\\|]*)_?([^_\\|]*)\\|?([^_\\|]*)$#', $id, $keys);
        $component = new $className($dao, $keys[1], $keys[2], $keys[3]);
        return $component;
    }

    private function _renderPage($page, $mode, $usePageTemplate)
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

        $response = $this->getResponse();
        if ($response->canSendHeaders()) {
            $response->setHeader('Content-Type', 'text/html');
        }
        $response->appendBody($body);
    }

    public function _renderFe($page, $mode)
    {
        if ($mode != 'fe') { return; }
        
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

}
