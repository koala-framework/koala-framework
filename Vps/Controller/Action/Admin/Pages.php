<?php
class Vps_Controller_Action_Admin_Pages extends Vps_Controller_Action
{
    protected $_auth = true;

    public function actionAction()
    {
        $this->view->ext('Vps.Admin.Pages.Index');
    }

    public function ajaxProcessPageDataAction()
    {
        $view = $this->view;
        $view->success = false;

        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        
        $action = $this->getRequest()->getParam('command');
        if ($action == 'delete') {
            $result = $table->deletePage($this->getRequest()->getParam('id'));
            $view->success = $result > 0;
        } else if ($action == 'move') {
            $source = $this->getRequest()->getParam('source');
            $target = $this->getRequest()->getParam('target');
            $point  = $this->getRequest()->getParam('point');
            $type = '';
            if ((int)$target == 0) {
                $rootData = $table->retrieveRootPageData();
                $type = $target;
                $target = $rootData['component_id'];
            }
            $view->success = $table->movePage($source, $target, $point, $type);
        } else {
            if ($action == 'add') {
                $type = '';
                $parentId = $this->getRequest()->getParam('parentId');
                $parentPageData = $table->retrievePageData($parentId);
                if (empty($parentPageData)) {
                    $parentPageData = $table->retrieveRootPageData();
                    if ((int)$parentId == 0) {
                        $type = $parentId;
                    }
                }
                if (!empty($parentPageData)) {
                    $id = $table->createPage($parentPageData['component_id'], $type);
                }
            } else if ($action == 'save') {
                $id = $this->getRequest()->getParam('id');
            }
            $pageData = $table->retrievePageData($id);
            if (!empty($pageData)) {
                $table->savePageName($pageData['component_id'], $this->getRequest()->getParam('name'));
                $table->savePageStatus($pageData['component_id'], $this->getRequest()->getParam('status') == 'on');
                
                $pageData = $table->retrievePageData($id);
                $view->data = $this->_getNodeData($pageData);
                $view->name = $pageData['name'];
                $view->status = $pageData['status'];
                $view->success = true;
            }
        }
    }

    public function ajaxLoadPageDataAction()
    {
        $name = '';
        $status = false;
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $pageData = $table->retrievePageData($this->getRequest()->getParam('id'));
        if (!empty($pageData)) {
            $name = $pageData['name'];
            $status = $pageData['status'];
        }
        $data[] = array('id' => 'name', 'value' => $name);
        $data[] = array('id' => 'status', 'value' => $status);
        $this->view->data = $data;
    }

    public function ajaxGetNodesAction()
    {
        $return = array();
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $type = null;

        $id = $this->getRequest()->getParam('node');
        if ((int)$id > 0){
            $parentPageData = $table->retrievePageData($id);
            $session = new Zend_Session_Namespace('admin');
            $openedPages = is_array($session->openedPages) ? $session->openedPages : array();
            $openedPages[$id] = true;
            $session->openedPages = $openedPages;
        } else {
            $config = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
            $types = $config->pagecollection->pagetypes->toArray();
            if (sizeof($types) > 0) {
                if ($id == 'root') {
                    foreach ($types as $type => $text) {
                        $data['id'] = $type;
                        $data['text'] = $text;
                        $data['leaf'] = false;
                        $data['cls'] = 'folder';
                        $data['expanded'] = true;
                        $data['allowDrag'] = false;
                        $return[] = $data;
                    }
                } else {
                    $type = $id;
                    $parentPageData = $table->retrieveRootPageData();
                }
            } else {
                $parentPageData = $table->retrieveRootPageData();
            }
        }

        if (!empty($parentPageData)) {
            $parentId = $parentPageData['component_id'];
            foreach ($table->retrieveChildPagesData($parentId, $type) as $pageData) {
                $return[] = $this->_getNodeData($pageData);
            }
        }

        $this->view->nodes = $return;
    }
    
    public function ajaxCollapseNodeAction()
    {
        $session = new Zend_Session_Namespace('admin');
        $openedPages = is_array($session->openedPages) ? $session->openedPages : array();
        $id = $this->getRequest()->getParam('id');
        if (isset($openedPages[$id])) {
            unset($openedPages[$id]);
        }
        $session->openedPages = $openedPages;
    }

    private function _getNodeData($pageData)
    {
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $session = new Zend_Session_Namespace('admin');
        $openedPages = is_array($session->openedPages) ? $session->openedPages : array();

        $d['id'] = $pageData['component_id'];
        $d['text'] = $pageData['name'];
        $d['leaf'] = false;
        $d['uiProvider'] = 'MyNodeUI';
        $d['status'] = $pageData['status'];
        if (sizeof($table->retrieveChildPagesData($d['id'])) > 0) {
            if (isset($openedPages[$d['id']])) {
                $d['expanded'] = true;
            } else {
                $d['expanded'] = false;
            }
        } else {
            $d['children'] = array();
            $d['expanded'] = true;
        }
        return $d;
    }

}
