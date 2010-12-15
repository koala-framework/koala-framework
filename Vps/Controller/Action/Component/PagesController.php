<?php
class Vps_Controller_Action_Component_PagesController extends Vps_Controller_Action_Auto_Tree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_buttons = array();
    protected $_hasPosition = true;
    protected $_modelName = 'Vps_Component_Model';

    private $_componentConfigs = array();

    protected function _init()
    {
        $this->_filters->add(new Vps_Controller_Action_Auto_Filter_Text())
            ->setQueryFields(array('name'));
    }

    public function indexAction()
    {
        $this->view->xtype = 'vps.component.pages';
    }

    protected function _formatNode($row)
    {
        $component = $row->getData();

        $data = parent::_formatNode($row);
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        $disabled = !Vps_Registry::get('acl')->getComponentAcl()
            ->isAllowed(Zend_Registry::get('userModel')->getAuthedUser(), $component);

        $data['actions'] = array();
        $data['allowDrop'] = false;
        $data['disabled'] = $disabled;
        $data['editControllerUrl'] = '';
        if ($component->componentId == 'root') { // Root hat keinen Generator
            $data['bIcon'] = new Vps_Asset('world');
            $data['bIcon'] = $data['bIcon']->__toString();
            $data['expanded'] = true;
        } else {
            $data = array_merge($data, $component->generator->getPagesControllerConfig($component));

            if ($disabled) $data['iconEffects'][] = 'forbidden';
            $icon = $data['icon'];
            if (is_string($icon)) {
                $icon = new Vps_Asset($icon);
            }
            $data['bIcon'] = $icon->toString($data['iconEffects']);
            if (isset($data['icon'])) unset($data['icon']);
        }

        $data['actions'] = array_merge(array(
            'properties' => false,
            'delete' => false,
            'visible' => false,
            'makeHome' => false,
            'add' => false,
            'preview' => false
        ), $data['actions']);


        // EditComponents
        $ec = array();
        foreach ($this->getEditComponents($component) as $c) {
            $ec = array_merge($ec, $this->_formatEditComponents($c->componentClass, $c->dbId, Vpc_Admin::EXT_CONFIG_DEFAULT));
        }
        foreach ($this->getMenuEditComponents($component) as $c) {
            $ec = array_merge($ec, $this->_formatEditComponents($c->componentClass, $c->dbId, Vpc_Admin::EXT_CONFIG_DEFAULT));
        }
        foreach ($this->getSharedComponents($component) as $componentClass => $c) {
            $ec = array_merge($ec, $this->_formatEditComponents($componentClass, $c->dbId, Vpc_Admin::EXT_CONFIG_SHARED));
        }

        $data['editComponents'] = $ec;
        return $data;
    }

    protected function _getParentId($row)
    {
        $parent = $row->parent;
        if (!$parent) return null;
        return $parent->componentId;
    }

    private function _formatEditComponents($componentClass, $dbId, $configType)
    {
        $ret = array();
        $cfg = Vpc_Admin::getInstance($componentClass)->getExtConfig($configType);
        if (isset($cfg['xtype'])) { //test for legacy
            throw new Vps_Exception("getExtConfig for $componentClass doesn't return an array of configs");
        }
        foreach ($cfg as $type=>$c) {
            $k = $componentClass.'-'.$type;
            if (!isset($this->_componentConfigs[$k])) {
                $this->_componentConfigs[$k] = $c;
            }
            $ret[] = array(
                'componentClass' => $componentClass,
                'type' => $type,
                'componentId' => $dbId
            );
        }
        return $ret;
    }

    // static zum Testen
    public static function getEditComponents($component)
    {
        $editComponents = array();
        if ($component->isPage) {
            $editComponents[] = $component;
        }
        $editComponents = array_merge($editComponents,
            $component->getRecursiveChildComponents(
                array(
                    'hasEditComponents' => true,
                    'ignoreVisible' => true,
                    'flags' => array('showInPageTreeAdmin' => false)
                ), array(
                    'flags' => array('showInPageTreeAdmin' => false)
                )
            )
        );
        return $editComponents;
    }

    // static zum Testen
    public static function getMenuEditComponents($component)
    {
        static $menuClasses = null;
        if (!is_array($menuClasses)) {
            $componentClasses = Vpc_Abstract::getComponentClasses();
            $menuClasses = array();
            foreach ($componentClasses as $class) {
                if (is_instance_of($class, 'Vpc_Menu_Abstract_Component') &&
                    Vpc_Abstract::hasSetting($class, 'level') &&
                    Vpc_Abstract::getSetting($class, 'showAsEditComponent')
                ) {
                    $menuClasses[Vpc_Abstract::getSetting($class, 'level')] = $class;
                }
            }
        }
        $editComponents = array();
        foreach ($menuClasses as $level => $class) {
            $menuComponents = $component->getChildComponents(array(
                'componentClasses' => array($class)
            ));
            foreach ($menuComponents as $menuComponent) {
                $c = $menuComponent->getComponent()->getMenuComponent();
                if ($c) $editComponents[] = $c;
            }
        }
        return $editComponents;
    }

    // static zum Testen
    public static function getSharedComponents($component)
    {
        static $sharedClasses = null;
        if (!is_array($sharedClasses)) {
            $componentClasses = Vpc_Abstract::getComponentClasses();
            $sharedClasses = array();
            foreach ($componentClasses as $componentClass) {
                $class = Vpc_Abstract::getFlag($componentClass, 'sharedDataClass');
                if ($class) $sharedClasses[$componentClass] = $class;
            }
        }
        $ret = array();
        foreach ($sharedClasses as $componentClass => $sharedClass) {
            $targetComponent = null;
            if (is_instance_of($component->componentClass, $sharedClass)) {
                $targetComponent = $component;
            }
            if (!$targetComponent) {
                $components = $component->getRecursiveChildComponents(
                    array('componentClass' => $sharedClass, 'pseudoPage' => false)
                );
                if (count($components) > 0) $targetComponent = array_shift($components);
            }
            if ($targetComponent) $ret[$componentClass] = $targetComponent;
        }
        return $ret;
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
        parent::jsonMoveAction(); // TODO: chained Rows mitmoven

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
        $config = $row->getData()->generator->getPagesControllerConfig($row->getData());
        $icon = new Vps_Asset($config['icon']);
        $this->view->icon = $icon->toString($config['iconEffects']);
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
        if (!$row instanceof Vpc_Root_Category_GeneratorRow) return;
        $m = Vps_Model_Abstract::getInstance('Vpc_Root_Category_GeneratorModel');
        $pageRow = $m->getRow($row->getData()->row->id);
        $r = $pageRow;
        while ($r) {
            if (!$r->visible) {
                //wenn seite offline ist ignorieren
                //  ist nicht natürlich nicht korrekt, wir *müssten* die überprüfung
                //  nachholen, sobald die seite online gestellt wird
                return;
            }
            $r = $r->getParentNode();
        }
        $components = $pageRow->getComponentsDependingOnRow();
        if ($components) {
            $msg = trlVps("You can not {0} this entry as it is used on the following pages:", $msgMethod);
            $msg .= Vps_Util_Component::getHtmlLocations($components);
            throw new Vps_ClientException($msg);
        }
    }

}
