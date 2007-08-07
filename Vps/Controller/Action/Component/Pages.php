<?php
class Vps_Controller_Action_Component_Pages extends Vps_Controller_Action_Auto_Tree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_icons = array (
        'default' => 'page',
        'invisible' => 'page_red',
        'reload' => 'control_repeat_blue',
        'add' => 'page_add',
        'delete' => 'page_delete',
        'folder' => 'folder'
    );
    
    public function indexAction()
    {
        $this->view->ext('Vps.Component.Pages');
    }
    
    public function init()
    {
        $this->_table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        parent::init();
    }
    
    public function jsonSavePageAction()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $this->_table->savePageName($id, $this->getRequest()->getParam('name'));
            $pageData = $this->_table->retrievePageData($id);
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
            
            $pageData = $this->_table->retrieveRootPageData();
            $data = $this->_formatNode($this->_table->find($pageData['id'])->current());
            $data['children'] = array();
            $data['expanded'] = true;
            $data['allowDrag'] = false;
            $data['allowDrop'] = false;
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
                $data['bIcon'] = 'folder_page';
                $data['uiProvider'] = 'Vps.AutoTree.Node';
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
            $rootPageData = $this->_table->retrieveRootPageData();
            $where['parent_id = ?'] = $rootPageData['id'];
            $where['type = ?'] = $node;
        } else {
            $where['parent_id = ?'] = $node;
        }
        return $where;
    }
    
}
