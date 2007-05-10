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

    public function ajaxSavePageDataAction()
    {
        $return['success'] = false;

        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);
        $page = null;
        $dao = $pageCollection->getDao();
        
        $action = $this->getRequest()->getParam('command');
        if ($action == 'delete') {
            $result = $dao->deletePage($this->getRequest()->getParam('id'));
            $return['success'] = $result > 0;
        } else if ($action == 'move') {
            $source = $this->getRequest()->getParam('source');
            $target = $this->getRequest()->getParam('target');
            $point  = $this->getRequest()->getParam('point');
            return $dao->movePage($source, $target, $point);
        } else {
            if ($action == 'add') {
                $parentPage = $pageCollection->getPageById($this->getRequest()->getParam('parentId'));
                if (!$parentPage) {
                    $parentPage = $this->getRootPage();
                }
                if ($parentPage) {
                    $id = $dao->createPage($parentPage->getId());
                    if ($id > 0) {
                        $page = $pageCollection->addPage($id);
                    }
                }
            } else if ($action == 'save') {
                $page = $pageCollection->getPageById($this->getRequest()->getParam('id'));
            }
            if ($page) {
                $dao->savePageName($page->getId(), $this->getRequest()->getParam('name'));
                $dao->savePageStatus($page->getId(), $this->getRequest()->getParam('status') == 'on');
                $pageData = $pageCollection->getPageData($page);
    
                $return['data'] = $this->_getNodeData($page);
                $return['name'] = $pageData['name'];
                $return['status'] = $pageData['status'];
                $return['success'] = true;
            }
        }

        $body = str_replace('"[]"', '[]', Zend_Json::encode($return));
        $this->getResponse()->setBody($body);
    }

    public function ajaxLoadPageDataAction()
    {
        $return = array();
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);
        $page = $pageCollection->getPageById($this->getRequest()->getParam('id'));
        if ($page) {
            $pageData = $pageCollection->getPageData($page);
            $data[] = array('id' => 'name', 'value' => $pageData['name']);
            $data[] = array('id' => 'status', 'value' => $pageData['status']);
            $return['success'] = true;
            $return['data'] = $data;
        } else {
            $return['success'] = false;
        }

        $this->getResponse()->setBody(Zend_Json::encode($return));
    }

    public function ajaxGetNodesAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);

        $id = $this->getRequest()->getParam('node');
        if ($id == 'root') { $id = $pageCollection->getRootPage()->getId(); }

        $data = array();
        $parentPage = $pageCollection->getPageById($id);
        if ($parentPage instanceof Vps_Component_Interface) {
            foreach ($pageCollection->getChildPages($parentPage) as $page) {
                $data[] = $this->_getNodeData($page);
            }
        }
        $body = str_replace('"[]"', '[]', Zend_Json::encode($data));
        $this->getResponse()->setBody($body);
    }
    
    private function _getNodeData($page)
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);

        $session = new Zend_Session_Namespace('admin');
        $expandedPath = $session->sitetreePath;
        $ids = $pageCollection->getIdsForPath($expandedPath);

        $pageData = $pageCollection->getPageData($page);
        $d['id'] = $page->getId();
        $d['text'] = $pageData['name'];
        $d['leaf'] = false;
        $d['cls'] = 'file';
        $d['uiProvider'] = 'MyNodeUI';
        $d['status'] = $pageData['status'];
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
        return $d;
    }

}
