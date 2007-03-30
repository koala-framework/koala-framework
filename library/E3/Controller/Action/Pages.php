<?php
class E3_Controller_Action_Pages extends E3_Controller_Action
{
    public function actionAction()
    {
        $pageCollection = E3_PageCollection_Abstract::getInstance();
        $data = array();
        $data2 = array();

        // Pages unter Root
        $rootPage = $pageCollection->getRootPage();
        $rootId = $rootPage->getId();
        foreach ($pageCollection->getChildPages($rootPage) as $page) {
            $data[$page->getId()] = $this->_getPageData($page);
            $data[$page->getId()]['parent'] = $rootId;
            $data[$page->getId()]['expanded'] = false;
        }
        
        // Von aktuelle Seite nach oben alle Seiten mitschreiben
        $page = $pageCollection->getPageByPath($this->getRequest()->getParam('path'));
        while ($page instanceof E3_Component_Interface && $page->getId() != $pageCollection->getRootPage()->getId()) {
            $tree[] = $page->getId();
            $page = $pageCollection->getParentPage($page);
        }

        // Alle Seiten, die auf Pfad zu aktueller Seite liegen, aufnehmen
        foreach (array_reverse($tree) as $id) {
            $parentPage = $pageCollection->getPageById($id);
            $data[$id]['expanded'] = true;
            $data[$id]['hasChildren'] = false;
            foreach ($pageCollection->getChildPages($parentPage) as $page) {
                $data[$page->getId()] = $this->_getPageData($page);
                $data[$page->getId()]['parent'] = $id;
                $data[$page->getId()]['expanded'] = in_array($page->getId(), $tree);
                if (in_array($page->getId(), $tree)) {
                    $data[$page->getId()]['hasChildren'] = false;
                }
                    
            }
        }

        $options['compile_dir'] = '../application/views_c';
        $options['left_delimiter'] = '<';
        $options['right_delimiter'] = '>';
        $options['debugging'] = true;
        $view = new E3_View_Smarty('../library/E3/Controller/Action', $options);
        $view->assign('rootId', $rootId);
        $view->assign('data', $data);
        $body = $view->render('Pages.html');

        $response = $this->getResponse();
        $response->appendBody($body);
    }
    
    public function ajaxGetChildNodesAction()
    {
        $data = array();
        $pageCollection = E3_PageCollection_Abstract::getInstance();
        $parentPage = $pageCollection->getPageById($this->getRequest()->getParam('id'));
        if ($parentPage instanceof E3_Component_Interface) {
            foreach ($pageCollection->getChildPages($parentPage) as $page) {
                $data[] = $this->_getPageData($page);
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    private function _getPageData($page)
    {
        $pageCollection = E3_PageCollection_Abstract::getInstance();
        $data['id'] = $page->getId();
        $data['name'] = $pageCollection->getFilename($page);
        $data['path'] = $pageCollection->getPath($page);
        if (sizeof($pageCollection->getChildPages($page)) > 0) {
            $data['hasChildren'] = true;
        } else {
            $data['hasChildren'] = false;
        }
        return $data;
    }
    
}
