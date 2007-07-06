<?php
class Vps_Controller_Action_Admin_Pages extends Vps_Controller_Action_AutoTree
{
    protected $_treeTextField = 'name';
    protected $_rootVisible = false;
    protected $_treeIcons = array (
        'default' => 'page',
        'invisible' => 'page_red',
        'reload' => 'control_repeat_blue',
        'add' => 'page_add',
        'delete' => 'page_delete',
        'folder' => 'folder'
    );
    
    public function actionAction()
    {
        $this->view->ext('Vps.Admin.Pages.Index');
    }
    
    public function init()
    {
        $this->_treeTable = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        parent::init();
    }
    
    public function jsonSavePageAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $this->_treeTable->savePageName($id, $this->getRequest()->getParam('name'));
            $pageData = $this->_treeTable->retrievePageData($id);
            $this->view->id = $id;
            $this->view->name = $pageData['name'];
        } catch (Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }

    public function jsonDataAction()
    {
        $id = $this->getRequest()->getParam('node');
        if ($id === '0') {
            
            $pageData = $this->_treeTable->retrieveRootPageData();
            $data = $this->_formatNode($this->_treeTable->find($pageData['id'])->current());
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
            $this->view->nodes = $return;

        } else {

            parent::jsonDataAction();

        }

    }
    
    protected function _getWhere()
    {
        $where = array();
        $node = $this->getRequest()->getParam('node');
        if ((int)$node === 0 && $node !== '0') {
            $rootPageData = $this->_treeTable->retrieveRootPageData();
            $where['parent_id = ?'] = $rootPageData['id'];
            $where['type = ?'] = $node;
        } else {
            $where['parent_id = ?'] = $node;
        }
        return $where;
    }
    
}
