<?php
class Vps_Controller_Action_Component_PagesController extends Vps_Controller_Action_Auto_Tree
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
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->ext('Vps.Component.Pages');
    }

    public function init()
    {
        $this->_table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $this->_table->showInvisible(true);
        parent::init();
    }

    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        if ($row->visible) {
            $data['bIcon'] = Vpc_Abstract::getSetting($row->component_class, 'componentIcon');
        }
        if ($row->is_home) {
            $data['bIcon'] = new Vps_Asset('application_home');
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        return $data;
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

            $config = new Zend_Config_Ini('application/config.ini', 'pagecollection');
            $types = $config->pagecollection->pagetypes->toArray();
            if (sizeof($types) == 0) $types[''] = 'Seiten';
            foreach ($types as $type => $text) {
                $data = array();
                $data['id'] = $type;
                $data['text'] = $text;
                $data['leaf'] = false;
                $data['expanded'] = true;
                $data['allowDrag'] = false;
                $data['type'] = 'category';
                $data['bIcon'] = new Vps_Asset('folder_page');
                $data['uiProvider'] = 'Vps.Component.PagesNode';
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
            $where['parent_id IS NULL'] = '';
            $where['type = ?'] = $node;
        } else {
            $where['parent_id = ?'] = $node;
        }
        return $where;
    }

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_table->find($id)->current();
        if ($row->is_home) {
            throw new Vps_ClientException('Cannot set Home Page invisible');
        } else {
            parent::jsonVisibleAction();
        }
    }

    public function jsonMakeHomeAction()
    {
        $id = $this->_getParam('id');
        $row = $this->_table->find($id)->current();
        if ($row) {
            $oldRows = $this->_table->fetchAll("is_home=1 AND id!='$id'");
            $oldId = $id;
            $oldVisible = false;
            foreach ($oldRows as $oldRow) {
                $oldId = $oldRow->id;
                $oldVisible = $oldRow->visible;
                $oldRow->is_home = 0;
                $oldRow->save();
            }

            $row->is_home = 1;
            $row->save();
            $this->view->home = $id;
            $this->view->oldhome = $oldId;
            $this->view->oldhomeVisible = $oldVisible;
        } else {
            $this->view->error = 'Node not found';
        }
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($this->_table->deletePage($id)) {
            $this->view->id = $id;
        }
    }
}
