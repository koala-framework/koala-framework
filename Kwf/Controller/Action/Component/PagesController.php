<?php
class Kwf_Controller_Action_Component_PagesController extends Kwf_Controller_Action_Component_PagesAbstractController
{
    private $_componentConfigs = array();

    protected function _getNodeConfig($component)
    {
        $user = Zend_Registry::get('userModel')->getAuthedUser();
        $acl = Kwf_Registry::get('acl')->getComponentAcl();
        return self::getNodeConfig($component, $user, $acl, $this->_componentConfigs);
    }

    //public static zum testen
    public static function getNodeConfig($component, $user, $acl, array &$componentConfigs = array())
    {
        $data = parent::getNodeConfig($component);
        $enabled = $acl->isAllowed($user, $component);
        if (!$enabled && !$component instanceof Kwf_Component_Data_Root/*root nicht überprüfen, die wird immar angezeigt*/) {
            $allowedComponents = $acl->getAllowedRecursiveChildComponents($user);
            $allowed = false;
            foreach ($allowedComponents as $allowedComponent) {
                $c = $allowedComponent;
                while ($c) {
                    if ($c->componentId == $component->componentId) {
                        if (!$allowedComponent->isPage) {
                            if (self::_getAllEditComponents(array($allowedComponent), $user, $acl)) {
                                //es gibt bearbeiten buttons, seite anzeigen
                                $allowed = true;
                                break;
                            }
                            if (!$allowed) {
                                 //nächste allowComponent überprüfen
                                 continue 2;
                            }
                        } else {
                            //seiten sind immer erlaubt (wegen seiteneigenschaften)
                            $allowed = true;
                        }
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

        $data['disabled'] = !$enabled;

        if ($component->componentId != 'root' && !$enabled) {
            $data['iconEffects'][] = 'forbidden';
        }

        if (!$acl->isAllowed($user, $component)) {
            //wenn komponente *selbst* nicht bearbeitbar ist actions deaktivieren
            //(in dem fall ist eine unterkomponente der seite bearbeitbar)
            $data['actions'] = array_merge($data['actions'], array(
                'delete' => false,
                'copy' => false,
                'visible' => false,
                'makeHome' => false,
            ));
            $data['allowDrag'] = false;
        }

        //wenn *unter* der seite eine page möglich ist (pageGenerator vorhanden) dann
        //hinzufügen + drop erlauben
        //das kann nicht im Generator ermittelt werden, der macht nur sich selbst
        if ($acl->isAllowed($user, $component)) {
            $pageGenerator = Kwf_Component_Generator_Abstract::getInstances($component, array(
                'pageGenerator' => true
            ));
            if ($pageGenerator) {
                $data['actions']['add'] = true;
                $data['actions']['paste'] = true;
                $data['allowDrop'] = true;
            }
        }
        $data['actions']['preview'] = (bool)$component->isPage;

        //default werte
        $data['actions'] = array_merge(array(
            'delete' => false,
            'copy' => false,
            'paste' => false,
            'visible' => false,
            'makeHome' => false,
            'add' => false,
        ), $data['actions']);

        $data['editComponents'] = self::_getAllEditComponents($editComponents, $user, $acl, $componentConfigs);

        return $data;
    }

    private static function _getAllEditComponents($editComponents, $user, $acl, array &$componentConfigs = array())
    {
        $ec = array();
        foreach ($editComponents as $editComponent) {
            foreach (self::getEditComponents($editComponent) as $c) {
                if (!$acl->isAllowed($user, $c)) continue;
                $ec = array_merge($ec, self::_formatEditComponents($c->componentClass, $c, Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT, $componentConfigs));
            }
            foreach (self::getSharedComponents($editComponent) as $componentClass => $c) {
                if (!$acl->isAllowed($user, $c)) continue;
                $ec = array_merge($ec, self::_formatEditComponents($componentClass, $c, Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_SHARED, $componentConfigs));
            }
        }
        return $ec;
    }

    private static function _formatEditComponents($componentClass, Kwf_Component_Data $component, $configType, &$componentConfigs)
    {
        $ret = array();
        $cfg = Kwc_Admin::getInstance($componentClass)->getExtConfig($configType);
        if (isset($cfg['xtype'])) { //test for legacy
            throw new Kwf_Exception("getExtConfig for $componentClass doesn't return an array of configs");
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
                $admin = Kwc_Admin::getInstance(get_class($generatorPlugin));
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
                    'generatorFlags' => array('showInPageTreeAdmin' => false),
                ), array(
                    'generatorFlags' => array('showInPageTreeAdmin' => false),
                    'hasEditComponents' => true,
                )
            )
        );
        return $editComponents;
    }

    // static zum Testen
    public static function getSharedComponents($component)
    {
        static $sharedClasses = array();
        if (!isset($sharedClasses[Kwf_Component_Data_Root::getComponentClass()])) { //pro root klasse cachen weil die sich ja bei den tests ändern kann
            $componentClasses = Kwc_Abstract::getComponentClasses();
            $sharedClasses[Kwf_Component_Data_Root::getComponentClass()] = array();
            foreach ($componentClasses as $componentClass) {
                $class = Kwc_Abstract::getFlag($componentClass, 'sharedDataClass');
                if ($class) $sharedClasses[Kwf_Component_Data_Root::getComponentClass()][$componentClass] = $class;
            }
        }
        $ret = array();
        foreach ($sharedClasses[Kwf_Component_Data_Root::getComponentClass()] as $componentClass => $sharedClass) {
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
        $row = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel')->getRow($id);
        if (!$row) {
            throw new Kwf_Exception("didn't find row to set home");
        }
        if (!$this->_hasPermissions($row, 'makeHome')) {
            throw new Kwf_Exception("Making home this row is not allowed.");
        }
        $root = Kwf_Component_Data_Root::getInstance();
        $component = $root->getComponentById($id, array('ignoreVisible' => true));

        if (!$component->generator->getGeneratorFlag('hasHome')) {
            throw new Kwf_Exception("Generator doesn't have hasHome flag set");
        }

        if (get_class($component) != 'Kwf_Component_Data') {
            //da die data klasse auf Kwf_Component_Data_Home angepasst geändert muss kann das nicht
            //gleichzeitig FirstChildPage oder LinkIntern sein. Daher verbieten.
            $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($component->componentClass, 'componentName'));
            throw new Kwf_Exception_Client(trlKwf("You can't set {0} as Home", $name));
        }

        while ($component) {
            if (Kwc_Abstract::getFlag($component->componentClass, 'hasHome')) {
                $homeComponent = $component;
                $component = null;
            } else {
                $component = $component->parent;
            }
        }

        if ($row) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('is_home', true);
            $s->whereNotEquals('id', $id);
            $oldRows = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel')->getRows($s);
            $oldId = $id;
            $oldVisible = false;
            foreach ($oldRows as $oldRow) {
                $component = $root->getComponentById($oldRow->id, array('ignoreVisible' => true));
                while ($component) {
                    if (Kwc_Abstract::getFlag($component->componentClass, 'hasHome')) {
                        if ($component === $homeComponent) {
                            $oldId = $oldRow->id;
                            $oldVisible = $oldRow->visible;
                            if (!$this->_hasPermissions($oldRow, 'makeHome')) {
                                throw new Kwf_Exception("Making home this row is not allowed.");
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
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($target, array('ignoreVisible' => true));
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
        $page = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('page_id'), array('ignoreVisible' => true));
        if (!$page) {
            throw new Kwf_Exception_Client(trlKwf('Page not found'));
        }
        Kwf_Component_Data_Root::setShowInvisible(true); //required to get correct url for eg. FirstChildPage with invisible children
        header('Location: '.$page->getPreviewUrl());
        exit;
    }

    protected function _changeVisibility(Kwf_Model_Row_Interface $row)
    {
        parent::_changeVisibility($row);
        $config = $row->getData()->generator->getPagesControllerConfig($row->getData());
        $icon = new Kwf_Asset($config['icon']);
        $this->view->icon = $icon->toString($config['iconEffects']);
        if (isset($row->getData()->isHome) && $row->getData()->isHome) {
            throw new Kwf_ClientException(trlKwf('Cannot set Home Page invisible'));
        }
        if (!$row->visible) {
            $this->_checkRowIndependence($row, trlKwf('hide'));
        }
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeDelete($row);
        $this->_checkRowIndependence($row, trlKwf('delete'));
    }

    private function _checkRowIndependence(Kwf_Model_Row_Interface $row, $msgMethod)
    {
        if (!$row instanceof Kwc_Root_Category_GeneratorRow) return;
        $m = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel');
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
            $msg = trlKwf("You can not {0} this entry as it is used on the following pages:", $msgMethod);
            $msg .= Kwf_Util_Component::getHtmlLocations($components);
            throw new Kwf_ClientException($msg);
        }
    }

    protected function _hasPermissions($row, $action)
    {
        $user = Zend_Registry::get('userModel')->getAuthedUser();
        if ($row instanceof Kwf_Component_Model_Row) {
            $component = $row->getData();
        } else {
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($row->id, array('ignoreVisible' => true));
        }

        // darf man die action?
        $config = $component->generator->getPagesControllerConfig($component);
        $actions = isset($config['actions']) ? $config['actions'] : array();
        $actions['move'] = $config['allowDrag'];
        $data['moveTo'] = !!Kwf_Component_Generator_Abstract::getInstances($component, array(
            'pageGenerator' => true
        ));
        if (in_array($action, array_keys($actions)) && !$actions[$action]) return false;

        // wenn ja, darf man die Komponente bearbeiten?
        $acl = Kwf_Registry::get('acl')->getComponentAcl();
        return $acl->isAllowed($user, $component);
    }

    public function jsonCopyAction()
    {
        $id = $this->_getParam('id');
        if (!Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception("Component with id '$id' not found");
        }
        $session = new Zend_Session_Namespace('PagesController:copy');
        $session->id = $id;
    }

    public function jsonPasteAction()
    {
        $session = new Zend_Session_Namespace('PagesController:copy');
        $id = $session->id;
        if (!$id || !Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception_Client(trlKwf('Clipboard is empty'));
        }
        $source = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
        $target = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('id'), array('ignoreVisible'=>true));

        $user = Zend_Registry::get('userModel')->getAuthedUser();
        $acl = Kwf_Registry::get('acl')->getComponentAcl();
        if (!$acl->isAllowed($user, $source) || !$acl->isAllowed($user, $target)) {
            throw new Kwf_Exception_AccessDenied();
        }

        Kwf_Component_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        $progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
            0, Kwf_Util_Component::getDuplicateProgressSteps($source)
        );

        ini_set('memory_limit', '256M');

        $newPage = Kwf_Util_Component::duplicate($source, $target, $progressBar);

        Kwf_Util_Component::afterDuplicate($source, $newPage);

        $progressBar->finish();


        $s = new Kwf_Model_Select();
        $s->whereEquals('parent_id', $newPage->row->parent_id);
        $s->order('pos', 'DESC');
        $s->limit(1);
        $lastRow = $newPage->generator->getModel()->getRow($s);
        $row = $newPage->generator->getModel()->getRow($newPage->row->id);
        $row->pos = $lastRow ? $lastRow->pos+1 : 1;
        $row->visible = false;
        $row->save();

        Kwf_Component_ModelObserver::getInstance()->enable();
    }
}
