<?php
class Vps_Controller_Action_Component_PagesController extends Vps_Controller_Action_Auto_Synctree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_icons = array (
        'default' => 'page',
        'invisible' => 'page_red',
        'reload' => 'arrow_rotate_clockwise',
        'add' => 'page_add',
        'delete' => 'page_delete',
        'folder' => 'folder',
        'home' => 'application_home',
        'domain' => 'world',
        'allowed' => 'page_white',
        'root' => 'world'
        );
    protected $_buttons = array();
    protected $_hasPosition = true;

    private $_componentConfigs = array();

    public function indexAction()
    {
        $this->view->xtype = 'vps.component.pages';
    }

    public function init()
    {
        $this->_model = Vps_Model_Abstract::getInstance('Vps_Component_Model');
        parent::init();
    }

    // Bei Domains Root ausblenden
    protected function _formatNodes($parentRow = null)
    {
        $root = Vps_Component_Data_Root::getInstance();
        if (is_instance_of($root->componentClass, 'Vpc_Root_DomainRoot_Component') && is_null($parentRow)) {
            //$parentRow = $this->_model->getRow($this->_model->select()->whereNull('parent_id'));
        }
        return parent::_formatNodes($parentRow);
    }

    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        if (!$row->visible) {
            $data['bIcon'] = $this->_icons['invisible']->__toString();
        }
        if ($row->isHome) {
            $data['bIcon'] = $this->_icons['home']->__toString();
        }
        $data['type'] = 'default';
        $data['allowed'] = Vps_Registry::get('acl')->getComponentAcl()
            ->isAllowed(Zend_Registry::get('userModel')->getAuthedUser(), $row->getData());

        if ($row->componentId == 'root') {
            $data['bIcon'] = $this->_icons['root']->__toString();
            $data['expanded'] = true;
            $data['type'] = 'root';
            $data['domain'] = null;
            $data['allowDrop'] = false;
        } else if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_Category_Component')) {
            $data['bIcon'] = $this->_icons['folder']->__toString();
            $data['expanded'] = $data['allowed'];
            $data['type'] = 'category';
            $domain = null;
            $domainComponent = $row->getData()->parent;
            if (is_instance_of($domainComponent->componentClass, 'Vpc_Root_DomainRoot_Domain_Component'))
                $domain = $row->getData()->parent->row->id;
            $data['domain'] = $domain;
            $data['category'] = $row->getData()->row->id;
        } else if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) {
            $data['bIcon'] = $this->_icons['domain']->__toString();
            $data['type'] = 'root';
            $data['expanded'] = $data['allowed'];
            $data['domain'] = $row->getData()->row->id;
            $data['allowDrop'] = false;
        } else {
            $data['domain'] = $row->getData()->row->domain;
            $data['category'] = $row->getData()->row->category;
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        if (!$data['allowed']) {
            $data['bIcon'] = $this->_icons['allowed']->__toString();
        }
        $data['data']['editComponents'] = array();

        $component = $row->getData();
        $editComponents = $component->getRecursiveChildComponents(
            array(
                'hasEditComponents' => true,
                'ignoreVisible' => true,
                'flags' => array('showInPageTreeAdmin' => false)
            ), array(
                'flags' => array('showInPageTreeAdmin' => false)
            )
        );
        if ($component->isPage) {
            $editComponents[] = $component;
        }
        $ec = array();
        foreach ($editComponents as $cc) {
            $cfg = Vpc_Admin::getInstance($cc->componentClass)->getExtConfig();
            if (isset($cfg['xtype'])) { //test for legacy
                throw new Vps_Exception("getExtConfig for $cc->componentClass doesn't return an array of configs");
            }
            foreach ($cfg as $type=>$c) {
                $k = $cc->componentClass.'-'.$type;
                if (!isset($this->_componentConfigs[$k])) {
                    $this->_componentConfigs[$k] = $c;
                }
                $ec[] = array(
                    'componentClass' => $cc->componentClass,
                    'type' => $type,
                    'componentId' => $cc->dbId
                );
            }
        }
        $data['data']['editComponents'] = $ec;
        return $data;
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->view->componentConfigs = $this->_componentConfigs;
    }

    public function jsonMakeHomeAction()
    {
        $id = $this->_getParam('id');
        $table = $this->_model->getTable();
        $row = $table->find($id)->current();
        if ($row) {
            $domain = $row->domain;
            $domainWhere = $domain ? "domain='$domain'" : "ISNULL(domain)";
            $oldRows = $table->fetchAll("is_home=1 AND id!='$id' AND $domainWhere");
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
            $this->view->error = 'Page not found';
        }
    }

    public function jsonMoveAction()
    {
        $target = $this->getRequest()->getParam('target');
        $component = Vps_Component_Data_Root::getInstance()->getComponentByDbId($target, array('ignoreVisible' => true));
        if ($component) {
            while ($component && !$this->_rootParentValue) {
                if (!$component->isPage) $this->_rootParentValue = $component->dbId;
                $component = $component->parent;
            }
        }
        parent::jsonMoveAction();

        $this->_rootParentValue = null;
    }

    protected function _beforeSaveMove($row) {
        $sourceRow = $this->_model->getTable()->find($this->getRequest()->getParam('source'))->current();
        $targetRow = $this->_model->getTable()->find($this->getRequest()->getParam('target'))->current();
        if ($sourceRow && $targetRow) {
            $sourceRow->category = $targetRow->category;
            $sourceRow->domain = $targetRow->domain;
            $sourceRow->save();
        }
    }

    public function openPreviewAction()
    {
        $host = $_SERVER['HTTP_HOST'];
        $host = str_replace('www.', '', $host);
        $host = 'preview.' . $host;
        $page = Vps_Component_Data_Root::getInstance()->getComponentById($this->_getParam('page_id'));
        if (!$page) {
            throw new Vps_ClientException(trlVps('Page not found'));
        }
        $href = 'http://' . $host . $page->url;
        header('Location: '.$href);
        exit;
    }
}
