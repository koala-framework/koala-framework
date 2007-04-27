<?php
class Vps_Controller_Action_Pages extends Vps_Controller_Action
{
    public function actionAction()
    {
        $session = new Zend_Session_Namespace('admin');
        $session->sitetreePath = $this->getRequest()->getParam('path');

        $view = new Vps_View_Smarty('../library/Vps/Controller/Action');
        $view->assign('expandedPath', $this->getRequest()->getParam('path'));
        $body = $view->render('Pages.html');

        $this->getResponse()->appendBody($body);
    }

    public function tabAction()
    {
        $view = new Vps_View_Smarty('../library/Vps/Controller/Action');
        $view->assign('expandedPath', $this->getRequest()->getParam('path'));
        $body = $view->render('PagesTree.html');

        $this->getResponse()->setBody($body);
    }

    public function saveAction()
    {
        $this->getResponse()->setBody('');
    }

    public function ajaxGetNodesAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);

        $session = new Zend_Session_Namespace('admin');
        $expandedPath = $session->sitetreePath;
        $ids = $pageCollection->getIdsForPath($expandedPath);

        $id = $this->getRequest()->getParam('node');
        if ($id == 'source') { $id = $pageCollection->getRootPage()->getId(); }

        $data = array();
        $parentPage = $pageCollection->getPageById($id);
        if ($parentPage instanceof Vps_Component_Interface) {
            foreach ($pageCollection->getChildPages($parentPage) as $page) {
                $dataData = $pageCollection->getPageData($page);
                $d['id'] = $page->getId();
                $d['text'] = $dataData['name'];
                $d['leaf'] = false;
                $d['cls'] = 'file';
                if (sizeof($pageCollection->getChildPages($page)) > 0) {
                    if (in_array($page->getId(), $ids)) {
                        $d['expanded'] = true;
                    } else {
                        $d['expanded'] = false;
                    }
                } else {
                    $d['children'] = '[]';
                    $d['expanded'] = true;
                }
                $data[] = $d;
            }
        }
        $body = str_replace('"[]"', '[]', Zend_Json::encode($data));
        $this->getResponse()->setBody($body);
    }

}
