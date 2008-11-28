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
        'root' => 'world'
        );
    protected $_buttons = array();
    protected $_hasPosition = true;

    public function indexAction()
    {
        $this->view->ext('Vps.Component.Pages');
    }

    public function init()
    {
        $this->_model = new Vps_Component_Model();
        parent::init();
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
        if ($row->componentId == 'root') {
            $data['bIcon'] = $this->_icons['root']->__toString();
            $data['expanded'] = true;
            $data['type'] = 'root';
            $data['domain'] = null;
        }
        if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_Category_Component')) {
            $data['bIcon'] = $this->_icons['folder']->__toString();
            $data['expanded'] = true;
            $data['type'] = 'category';
            $domain = null;
            $domainComponent = $row->getData()->parent;
            if ($domainComponent) $domain = $row->getData()->parent->row->id;
            $data['domain'] = $domain;
        }
        if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) {
            $data['bIcon'] = $this->_icons['domain']->__toString();
            $data['expanded'] = true;
            $data['type'] = 'root';
            $data['domain'] = null;
        }
        if (!array_key_exists('domain', $data)) {
            $data['domain'] = $row->getData()->row->domain;
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';

        $component = $row->getData();
        $editComponents = $component->getRecursiveChildComponents(
            array(
                'hasEditComponents' => true,
                'pageGenerator' => false
            )
        );
        if ($component->isPage) {
            $editComponents[] = $component;
        }
        $data['data']['editComponents'] = array();
        foreach ($editComponents as $cc) {
            if (!Vpc_Abstract::hasSetting($cc->componentClass, 'componentName')
                || !Vpc_Abstract::getSetting($cc->componentClass, 'componentName'))
            {
                //wenn das probleme verursact ignorieren - aber es erspart lange fehlersuche warum eine komp. nicht angezeigt wird :D
                throw new Vps_Exception("Component '$cc->componentClass' does have no componentName but must have one for editing");
            }

            $data['data']['editComponents'][] = array(
                'componentClass' => $cc->componentClass,
                'componentName' => Vpc_Abstract::getSetting($cc->componentClass, 'componentName'),
                'dbId' => $cc->dbId,
                'componentIcon' => Vpc_Abstract::getSetting($cc->componentClass, 'componentIcon')->__toString()
            );
        }
        return $data;
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
