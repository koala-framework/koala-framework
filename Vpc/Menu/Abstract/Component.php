<?php
abstract class Vpc_Menu_Abstract_Component extends Vpc_Abstract
{
    private $_currentPages;
    private $_config;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Menu');
        $ret['componentIcon'] = new Vps_Asset('layout');
        $ret['cssClass'] = 'webStandard';
        $ret['showParentPage'] = false;
        $ret['ownModel'] = 'Vpc_Menu_Abstract_Model';
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Menu/Abstract/Panel.js';
        $ret['showAsEditComponent'] = false;
        $ret['liCssClasses'] = array(
            'offset' => trlVps('Offset')
        );
        $ret['menuModel'] = 'Vpc_Menu_Abstract_MenuModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = null;
        if ($this->_getSetting('showParentPage')) {
            $currentPages = array_reverse($this->_getCurrentPages());
            if (isset($this->getData()->level)) {
                $level = $this->getData()->level;
            } else {
                $level = $this->_getSetting('level');
            }
            if (is_string($level)) {
                throw new Vps_Exception("You can't use showParentMenu for MainMenus (what should that do?)");
            }
            if (isset($currentPages[$level-2])) {
                $ret['parentPage'] = $currentPages[$level-2];
            }
        }
        return $ret;
    }

    public function getMenuComponent()
    {
        return $this->_getMenuData() ? $this->getData() : null;
    }

    public function getMenuData($parentData = null)
    {
        return $this->_getMenuData($parentData);
    }

    protected function _getMenuData($parentData = null)
    {
        $constraints = array('showInMenu' => true);
        $ret = array();
        $currentPages = array_reverse($this->_getCurrentPages());
        if ($parentData) {
            $ret = $parentData->getChildPages($constraints);
        } else {
            if (isset($this->getData()->level)) {
                $level = $this->getData()->level;
            } else {
                $level = $this->_getSetting('level');
            }
            if (is_string($level)) {
                $component = $this->getData()->parent;
                while ($component && !Vpc_Abstract::getFlag($component->componentClass, 'menuCategory')) {
                    $component = $component->parent;
                }
                $category = null;
                if (!$component) {
                    //wenn seite nicht _unter_ einer kategorie anders suchen
                    $component = $this->getData()->parent;
                    while ($component && !$category) {
                        $component = $component->parent;
                        $category = $component->getChildComponent('-' . $level);
                        if ($category && !Vpc_Abstract::getFlag($category->componentClass, 'menuCategory')) {
                            $category = false;
                        }
                    }
                } else if ($component->parent) {
                    $category = $component->parent->getChildComponent('-' . $level);
                } else {
                    $category = $component;
                }
                if (!$category) throw new Vps_Exception("Category-Component '$level' not found");
                $ret = $category->getChildPages($constraints);
            } else {
                if (isset($currentPages[$level-2])) {
                    $ret = $currentPages[$level-2]->getChildPages($constraints);
                }
            }
        }
        $currentPageIds = array();
        foreach ($currentPages as $page) {
            if (!$page instanceof Vps_Component_Data_Root) {
                $currentPageIds[] = $page->getComponentId();
            }
        }
        $i = 0;
        foreach ($ret as $r) {
            $class = array();
            if ($i == 0) { $class[] = 'first'; }
            if ($i == count($ret)-1) { $class[] = 'last'; }
            if (in_array($r->componentId, $currentPageIds)) {
                $class[] ='current';
                $r->current = true;
            }
            $cssClass = $this->_getConfig($r, 'cssClass');
            if ($cssClass) $class[] = $cssClass;
            $r->class = implode(' ', $class);
            $i++;
        }
        return $ret;
    }

    protected function _getConfig($component, $key = null)
    {
        $id = $component->componentId;
        if (!isset($this->_config[$id])) {
            $row = $this->getModel() ? $this->getModel()->getRow($id) : null;
            $this->_config[$id] = $row ? unserialize($row->data) : null;
        }
        $ret = $this->_config[$id];
        if ($key) {
            $ret = isset($ret[$key]) ? $ret[$key] : null;
        }
        return $ret;
    }

    // Array mit IDs von aktueller Seiten und Parent Pages
    protected function _getCurrentPages()
    {
        if (!isset($this->_currentPages)) {
            $this->_currentPages = array();
            $p = $this->getData()->getPage();
            while ($p) {
                $this->_currentPages[] = $p;
                $p = $p->getParentPage();
            }
            /*
            if (!$p) {
                throw new Vps_Exception('To show the menu currentPage has to be set for Vps_Component_Data_Root');
            }
            do {
                $this->_currentPages[] = $p;
            } while ($p = $p->getParentPage());
            */
        }
        return $this->_currentPages;
    }

    public static function getStaticCacheVars()
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $key => $generator) {
                if (!isset($generator['showInMenu']) || !$generator['showInMenu']) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $componentClass, array('generator' => $key))
                );
                if (!$generator->getGeneratorFlag('page') || !$generator->getGeneratorFlag('table')) continue;
                $ret[] = array(
                    'model' => $generator->getModel()
                );
            }
        }
        $ret[] = array(
            'model' => 'Vps_Component_Model'
        );
        // Falls Nickname geändert wird, ändert sich Url zum User
        $ret[] = array(
            'model' => Vps_Registry::get('config')->user->model
        );
        return $ret;
    }
}
