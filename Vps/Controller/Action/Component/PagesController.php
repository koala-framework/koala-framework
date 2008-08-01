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
        'folder' => 'folder',
        'home' => 'application_home',
        'root' => 'world'
        );
    protected $_buttons = array();

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
        if ($row->componentId == 'root') {
            $data['bIcon'] = $this->_icons['root']->__toString();
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';

        $component = $row->getData();
        $editComponents = array_merge(
            array($component), 
            $component->getChildComponents(
                array('hasEditComponents' => true), 
                array('skipRoot' => true, 'page' => false)
            )
        );
        $data['data']['editComponents'] = array();
        foreach ($editComponents as $cc) {
            if (Vpc_Abstract::hasSetting($cc->componentClass, 'componentName')
                && Vpc_Abstract::getSetting($cc->componentClass, 'componentName'))
            {
                $data['data']['editComponents'][] = array(
                    'componentClass' => $cc->componentClass,
                    'componentName' => Vpc_Abstract::getSetting($cc->componentClass, 'componentName'),
                    'dbId' => $cc->dbId,
                    'componentIcon' => Vpc_Abstract::getSetting($cc->componentClass, 'componentIcon')->__toString()
                );
            }
        }
        return $data;
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
