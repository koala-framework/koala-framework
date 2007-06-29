<?php
class Vps_Controller_Action_Admin_Pages extends Vps_Controller_Action
{
    protected $_auth = true;
    private $_pc = null;
    private $_openedPages;
    private $_table;

    public function actionAction()
    {
        $this->view->ext('Vps.Admin.Pages.Index');
    }
    
    public function init()
    {
        $session = new Zend_Session_Namespace('admin');
        $this->openedPages = is_array($session->openedPages) ? $session->openedPages : array();

        $this->_table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
    }

    public function jsonVisibleAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $visible = $this->getRequest()->getParam('visible') == 'true';
            
            $this->view->success = $this->_table->saveVisible($id, $visible);
            $pageData = $this->_table->retrievePageData($id);
            $this->view->visible = $pageData['visible'] == '1';
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }
    
    public function jsonSavePageAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $this->_table->savePageName($id, $this->getRequest()->getParam('name'));
            $pageData = $this->_table->retrievePageData($id);
            $this->view->name = $pageData['name'];
            $this->view->success = true;
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function jsonDeletePageAction()
    {
        try {
            $this->_table->deletePage($this->getRequest()->getParam('id'));
            $this->view->success = true;
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }
    
    public function jsonAddPageAction()
    {
        try {
            $parentId = $this->getRequest()->getParam('parentId');
            $name = $this->getRequest()->getParam('name');

            if ((int)$parentId == 0) {
                $rootPageData = $this->_table->retrieveRootPageData();
                $type = $parentId;
                $parentId = $rootPageData['component_id'];
            } else {
                $type = '';
            }

            $id = $this->_table->createPage($parentId, $type);
            $this->_table->savePageName($id, $name);

            $pageData = $this->_table->retrievePageData($id);
            $this->view->config = $this->_getNodeData($pageData);
            $this->view->success = true;

        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }
    
    public function jsonMovePageAction()
    {
        try {
            $source = $this->getRequest()->getParam('source');
            $target = $this->getRequest()->getParam('target');
            $point  = $this->getRequest()->getParam('point');
            $type = '';
            if ((int)$target == 0) {
                $rootData = $this->_table->retrieveRootPageData();
                $type = $target;
                $target = $rootData['component_id'];
            }
            $this->view->success = $this->_table->movePage($source, $target, $point, $type);
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function jsonLoadPageDataAction()
    {
        $name = '';
        $visible = false;
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $pageData = $table->retrievePageData($this->getRequest()->getParam('id'));
        if (!empty($pageData)) {
            $name = $pageData['name'];
            $visible = $pageData['visible'];
        }
        $data[] = array('id' => 'name', 'value' => $name);
        $data[] = array('id' => 'visible', 'value' => $visible);
        $this->view->data = $data;
    }

    public function jsonGetNodesAction()
    {
        $return = array();
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $type = null;

        $id = $this->getRequest()->getParam('node');
        if ((int)$id > 0) {
            
            $parentPageData = $table->retrievePageData($id);
            $session = new Zend_Session_Namespace('admin');
            $openedPages = is_array($session->openedPages) ? $session->openedPages : array();
            $openedPages[$id] = true;
            $session->openedPages = $openedPages;
        
        } else if ($id == 'root') {
            
            $pageData = $table->retrieveRootPageData();
            $data = $this->_getNodeData($pageData);
            $data['children'] = array();
            $data['expanded'] = true;
            $data['allowDrop'] = false;
            $data['allowDrag'] = false;
            $data['type'] = 'root';
            $return[] = $data;

            $config = new Zend_Config_Ini('application/config.ini', 'pagecollection');
            $types = $config->pagecollection->pagetypes->toArray();
            if (sizeof($types) == 0) { $types[''] = 'Seiten'; }
            foreach ($types as $type => $text) {
                $data = array();
                $data['id'] = $type;
                $data['text'] = $text;
                $data['leaf'] = false;
                $data['expanded'] = true;
                $data['allowDrag'] = false;
                $data['type'] = 'category';
                $data['cls'] = 'folder';
                $return[] = $data;
            }

        } else {

            $type = $id;
            $parentPageData = $table->retrieveRootPageData();

        }

        if (!empty($parentPageData)) {
            $parentId = $parentPageData['component_id'];
            foreach ($table->retrieveChildPagesData($parentId, $type) as $pageData) {
                $return[] = $this->_getNodeData($pageData);
            }
        }

        $this->view->nodes = $return;
    }
    
    public function jsonCollapseNodeAction()
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
        $d['visible'] = $pageData['visible'] == '1';
        if (!$d['visible']) {
            $d['cls'] = 'page_red';
        } else {
            $d['cls'] = 'page';
        }
        $d['type'] = 'default';
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
