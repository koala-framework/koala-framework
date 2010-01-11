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
            $data['allowDrop'] = false;
        } else if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_Category_Component')) {
            $data['bIcon'] = $this->_icons['folder']->__toString();
            $data['expanded'] = $data['allowed'];
            $data['type'] = 'category';
        } else if (is_instance_of($row->getData()->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) {
            $data['bIcon'] = $this->_icons['domain']->__toString();
            $data['type'] = 'root';
            $data['expanded'] = $data['allowed'];
            $data['allowDrop'] = false;
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
        $root = Vps_Component_Data_Root::getInstance();
        $component = $root->getComponentById($id, array('ignoreVisible' => true));
        while ($component) {
            if (Vpc_Abstract::getFlag($component->componentClass, 'hasHome')) {
                $homeComponent = $component;
                $component = null;
            } else {
                $component = $component->parent;
            }
        }

        if ($row) {
            $oldRows = $table->fetchAll("is_home=1 AND id!='$id'");
            $oldId = $id;
            $oldVisible = false;
            foreach ($oldRows as $oldRow) {
                $component = $root->getComponentById($oldRow->id, array('ignoreVisible' => true));
                while ($component) {
                    if (Vpc_Abstract::getFlag($component->componentClass, 'hasHome')) {
                        if ($component == $homeComponent) {
                            $oldId = $oldRow->id;
                            $oldVisible = $oldRow->visible;
                            $oldRow->is_home = 0;
                            $oldRow->save();
                        }
                        $component = null;
                    } else {
                        $component = $component->parent;
                    }
                }
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

    protected function _changeVisibility(Vps_Model_Row_Interface $row)
    {
        parent::_changeVisibility($row);
        if (!$row->visible) {
            $this->_checkRowIndependence($row, trlVps('hide'));
        }
    }

    protected function _beforeDelete(Vps_Model_Row_Interface $row)
    {
        parent::_beforeDelete($row);
        $this->_checkRowIndependence($row, trlVps('delete'));
    }

    private function _checkRowIndependence(Vps_Model_Row_Interface $row, $msgMethod)
    {
        $r = $row;
        while ($r) {
            if (!$r->visible) {
                //wenn seite offline ist ignorieren
                //  ist nicht natürlich nicht korrekt, wir *müssten* die überprüfung
                //  nachholen, sobald die seite online gestellt wird
                return;
            }
            $r = $row->getParentNode();
        }
        $components = $row->getComponentsDependingOnRow();
        if ($components) {
            $msg = trlVps("You can not {0} this entry as it is used on the following pages:", $msgMethod);
            foreach ($components as $c) {
                $msg .= "<br /><a href=\"$c->url\" target=\"_blank\">".$c->getTitle()."</a>";
            }
            throw new Vps_ClientException($msg);
        }
    }

}
