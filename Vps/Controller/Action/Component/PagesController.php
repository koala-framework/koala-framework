<?php
class Vps_Controller_Action_Component_PagesController extends Vps_Controller_Action_Auto_Synctree
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
        $this->_table = new Vps_Dao_Pages();
        parent::init();
    }


    //TODO: _formatNode vs. _formatNodes, dass darf es nur einmal geben!
    //gehört in Vps_Controller_Action_Auto_Synctree verbessert!!!
    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        $classes = Vpc_Abstract::getSetting(Vps_Registry::get('config')->vpc->rootComponent, 'childComponentClasses');
        $data['data']['component_class'] = $classes[$row->component];
        if ($row->visible) {
            $data['bIcon'] = Vpc_Abstract::getSetting($data['componentClass'], 'componentIcon');
        }
        if ($row->is_home) {
            $data['bIcon'] = new Vps_Asset('application_home');
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        return $data;
    }
    protected function _formatNodes($parentId = null)
    {
        $ret = parent::_formatNodes($parentId);
        $classes = Vpc_Abstract::getSetting(Vps_Registry::get('config')->vpc->rootComponent, 'childComponentClasses');
        foreach ($ret as &$node) {
            $node['data']['component_class'] = $classes[$node['data']['component']];
        }
        return $ret;
    }

    public function jsonDataAction()
    {
        $id = $this->getRequest()->getParam('node');

        if ($id === '0') {

            $types = Zend_Registry::get('config')->vpc->pageTypes->toArray();
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
                $data['bIcon'] = $data['bIcon']->__toString();
                $data['uiProvider'] = 'Vps.Component.PagesNode';
                $data['children'] = $this->_formatNodes($type);
                $return[] = $data;
            }
            $this->view->nodes = $return;

        } else {

            parent::jsonDataAction();

        }
    }

    protected function _getTreeWhere($parentId = null)
    {
        $where = array();
        $node = $this->getRequest()->getParam('node');
        if (is_string($parentId)) {
            $where['parent_id IS NULL'] = '';
            $where['type = ?'] = $parentId;
        } else {
            $where['parent_id = ?'] = $parentId;
        }
        return $where;
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

    public function openPreviewAction()
    {
        $host = $_SERVER['HTTP_HOST'];
        $host = str_replace('www.', '', $host);
        $host = 'preview.' . $host;
        $pc = Vps_PageCollection_Abstract::getInstance();
        $p = $pc->getPageById($this->_getParam('page_id'));
        $href = 'http://' . $host . $pc->getUrl($p);
        header('Location: '.$href);
        exit;
    }
}
