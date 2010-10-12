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

        $user = Zend_Registry::get('userModel')->getAuthedUser();
        $acl = Vps_Registry::get('acl')->getComponentAcl();

        $nodeConfig = self::getNodeConfig($component, $user, $acl, $this->_componentConfigs);
        if (is_null($nodeConfig)) return null;
        $data = array_merge($data, $nodeConfig);

        if (!$data['expanded']) {
            $openedNodes = $this->_saveSessionNodeOpened(null, null);
            if ($data['disabled'] && !array_key_exists($row->id, $openedNodes)) {
                $data['expanded'] = true;
            }
        }

        if ($data['loadChildren'] || $data['expanded'] || $data['disabled']) {
            $data['children'] = $this->_formatNodes($component->componentId);
        }

        return $data;
    }

    //public static zum testen
    public static function getNodeConfig($component, $user, $acl, array &$componentConfigs = array())
    {
        $data = array();
        $enabled = $acl->isAllowed($user, $component);
        if (!$enabled) {
//             static $allowedComponents;
//             if (!isset($allowedComponents)) {
                //TODO kann das wirklich gecached werden?
                $allowedComponents = $acl->getAllowedRecursiveChildComponents($user, $component);
//             }
            $allowed = false;
            foreach ($allowedComponents as $allowedComponent) {
                if (!$allowedComponent->isPage) { //wenns eine page ist muss sie immer angezeigt werden fuer seiteneigenschaften
                    if (!self::_getAllEditComponents(array($allowedComponent), $user, $acl)) {
                        //wenns bei der komponente nichts zu bearbeiten gibt wirds gar nicht angezeigt, auch ihre parents nicht
                        continue;
                    }
                }
                $c = $allowedComponent;
                while ($c) {
                    if ($c->componentId == $component->componentId) {
                        $allowed = true;
                        break 2;
                    }
                    $c = $c->parent;
                }
            }
            //wenn gar keine unterkomponente bearbeitet werden kann seite ausblenden
            if (!$allowed) return null;
        }
        if (!$enabled) {
            //wenn eine unterkomponente (nicht seite!) bearbeitet werden kann seite nicht ausgrauen
            $editComponents = $acl->getAllowedChildComponents($user, $component);
            if ($editComponents) $enabled = true;
        } else {
            $editComponents = array($component);
        }

        $data['actions'] = array();
        $data['allowDrop'] = false;
        $data['disabled'] = !$enabled;
        $data['editControllerUrl'] = '';

        if ($component->componentId == 'root') { // Root hat keinen Generator
            $data['bIcon'] = new Vps_Asset('world');
            $data['bIcon'] = $data['bIcon']->__toString();
            $data['expanded'] = true;
            $data['loadChildren'] = true;
        } else {
            $config = $component->generator->getPagesControllerConfig($component);
            $data = array_merge($data, $config);
            if (!$enabled) $data['iconEffects'][] = 'forbidden';
            $icon = $data['icon'];
            if (is_string($icon)) {
                $icon = new Vps_Asset($icon);
            }
            $data['bIcon'] = $icon->toString($data['iconEffects']);
            if (isset($data['icon'])) unset($data['icon']);
        }

        if (!$acl->isAllowed($user, $component)) {
            //wenn komponente *selbst* nicht bearbeitbar ist actions deaktivieren
            //(in dem fall ist eine unterkomponente der seite bearbeitbar)
            $data['actions'] = array_merge($data['actions'], array(
                'properties' => false,
                'delete' => false,
                'visible' => false,
                'makeHome' => false,
            ));
            $data['allowDrag'] = false;
        }


        //wenn *unter* der seite eine page möglich ist (pageGenerator vorhanden) dann
        //hinzufügen + drop erlauben
        //das kann nicht im Generator ermittelt werden, der macht nur sich selbst
        if ($acl->isAllowed($user, $component)) {
            $pageGenerator = Vps_Component_Generator_Abstract::getInstances($component, array(
                'pageGenerator' => true
            ));
            if ($pageGenerator) {
                $data['addControllerUrl'] = Vpc_Admin::getInstance($pageGenerator[0]->getClass())
                    ->getControllerUrl('Generator');
                $data['actions']['add'] = true;
                $data['allowDrop'] = true;
            }
        }

        $data['actions']['preview'] = (bool)$component->isPage;


        //default werte
        $data['actions'] = array_merge(array(
            'properties' => false,
            'delete' => false,
            'visible' => false,
            'makeHome' => false,
            'add' => false,
        ), $data['actions']);
        $data = array_merge(array(
            'allowDrag' => false,
            'allowDrop' => false
        ), $data);

        $data['editComponents'] = self::_getAllEditComponents($editComponents, $user, $acl, $componentConfigs);

        return $data;
    }

    private static function _getAllEditComponents($editComponents, $user, $acl, array &$componentConfigs = array())
    {
        $ec = array();
        foreach ($editComponents as $editComponent) {
            foreach (self::getEditComponents($editComponent) as $c) {
                if (!$acl->isAllowed($user, $c)) continue;
                $ec = array_merge($ec, self::_formatEditComponents($c->componentClass, $c, Vps_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT, $componentConfigs));
            }
            foreach (self::getMenuEditComponents($editComponent) as $c) {
                if (!$acl->isAllowed($user, $c)) continue;
                $ec = array_merge($ec, self::_formatEditComponents($c->componentClass, $c, Vps_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT, $componentConfigs));
            }
            foreach (self::getSharedComponents($editComponent) as $componentClass => $c) {
                if (!$acl->isAllowed($user, $c)) continue;
                $ec = array_merge($ec, self::_formatEditComponents($componentClass, $c, Vps_Component_Abstract_ExtConfig_Abstract::TYPE_SHARED, $componentConfigs));
            }
        }
        return $ec;
    }

    protected function _getParentId($row)
    {
        $parent = $row->parent;
        if (!$parent) return null;
        return $parent->componentId;
    }

    private static function _formatEditComponents($componentClass, Vps_Component_Data $component, $configType, &$componentConfigs)
    {
        $ret = array();
        $cfg = Vpc_Admin::getInstance($componentClass)->getExtConfig($configType);
        if (isset($cfg['xtype'])) { //test for legacy
            throw new Vps_Exception("getExtConfig for $componentClass doesn't return an array of configs");
        }
        foreach ($cfg as $type=>$c) {
            $k = $componentClass.'-'.$type;
            if (!isset($componentConfigs[$k])) {
                $componentConfigs[$k] = $c;
            }
            $ret[] = array(
                'componentClass' => $componentClass,
                'type' => $type,
                'componentId' => $component->dbId
            );
        }
        if (isset($component->generator)) { //nicht gesetzt bei root
            foreach ($component->generator->getGeneratorPlugins() as $generatorPlugin) {
                $admin = Vpc_Admin::getInstance(get_class($generatorPlugin));
                $cfg = $admin->getExtConfig($configType);
                foreach ($cfg as $type=>$c) {
                    $k = get_class($generatorPlugin).'-'.$type;
                    if (!isset($componentConfigs[$k])) {
                        $componentConfigs[$k] = $c;
                    }
                    $ret[] = array(
                        'componentClass' => get_class($generatorPlugin),
                        'type' => $type,
                        'componentId' => $component->dbId
                    );
                }
            }
        }
        return $ret;
    }

    // static zum Testen
    public static function getEditComponents($component)
    {
        $editComponents = array();

        //egal ob component eine page ist oder nicht, selbst darf sie immer bearbeitet werden
        //es ist dann *keine* page wenn der benutzer unter eine unterkomponente bearbeiten darf - nicht aber die page selbst
        $editComponents[] = $component;

        $editComponents = array_merge($editComponents,
            $component->getRecursiveChildComponents(
                array(
                    'hasEditComponents' => true,
                    'ignoreVisible' => true,
                    'flags' => array('showInPageTreeAdmin' => false)
                ), array(
                    'flags' => array('showInPageTreeAdmin' => false),
                    'hasEditComponents' => true,
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
        if (!$this->_hasPermissions($row, 'makeHome')) {
            throw new Vps_Exception("Making home this row is not allowed.");
        }
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
                            if (!$this->_hasPermissions($row, 'makeHome')) {
                                throw new Vps_Exception("Making home this row is not allowed.");
                            }
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

    protected function _hasPermissions($row, $action)
    {
        $user = Zend_Registry::get('userModel')->getAuthedUser();
        if ($row instanceof Vps_Component_Model_Row) {
            $component = $row->getData();
        } else {
            $component = Vps_Component_Data_Root::getInstance()
                ->getComponentById($row->id, array('ignoreVisible' => true));
        }

        // darf man die action?
        $config = $component->generator->getPagesControllerConfig($component);
        $actions = $config['actions'];
        $actions['move'] = $config['allowDrag'];
        $actions['moveTo'] = $config['allowDrop'];
        if (in_array($action, array_keys($actions)) && !$actions[$action]) return false;

        // wenn ja, darf man die Komponente bearbeiten?
        $acl = Vps_Registry::get('acl')->getComponentAcl();
        return $acl->isAllowed($user, $component);
    }
}
