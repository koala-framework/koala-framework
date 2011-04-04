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
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Menu/Abstract/Panel.js';
        $ret['showAsEditComponent'] = false;
        $ret['liCssClasses'] = array(
            'offset' => trlVps('Offset')
        );
        $ret['level'] = 'main';
        $ret['dataModel'] = 'Vpc_Menu_Abstract_Model';
        $ret['menuModel'] = 'Vpc_Menu_Abstract_MenuModel';
        $ret['flags']['alternativeComponent'] = 'Vpc_Basic_ParentContent_Component';

        $ret['extConfig'] = 'Vpc_Menu_Abstract_ExtConfig';
        return $ret;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $level = self::_getMenuLevel($componentClass, $parentData, $generator);
        $maxLevel = (int)Vpc_Abstract::getSetting($componentClass, 'level');
        return $level > $maxLevel;
    }

    protected static function _getMenuLevel($componentClass, $parentData, Vps_Component_Generator_Abstract $generator)
    {
        $data = $parentData;
        $level = $generator->getGeneratorFlag('page') ? 1 : 0; // falls zu erstellendes Data eigene Page ist (tritt eigentlich nur bei Tests auf)
        while ($data && !Vpc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
            if ($data->isPage) $level++;
            $data = $data->parent;
        }
        return $level;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPage'] = null;
        if ($this->_getSetting('showParentPage')) {
            $currentPages = array_reverse($this->_getCurrentPagesCached());
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
        $component = $this->getData()->parent;
        $level = 1;
        while ($component) {
            $menuCategory = Vpc_Abstract::getFlag($component->componentClass, 'menuCategory');
            if ($menuCategory) {
                $component = null;
                if ($level == 1) $level = $menuCategory;
            } else {
                $component = $component->parent;
                $level++;
            }
        }
        if ($level == $this->_getSetting('level') && $this->_getMenuData()) {
            return $this->getData();
        }
        return null;
    }

    public function getMenuData($parentData = null, $select = array())
    {
        return $this->_getMenuData($parentData, $select);
    }

    public function getPageComponent($parentData = null)
    {
        $ret = null;

        $ret = array();
        $currentPages = array_reverse($this->_getCurrentPagesCached());
        if ($parentData) {
            $ret = $parentData;
        } else {
            if (isset($this->getData()->level)) {
                $level = $this->getData()->level;
            } else if ($this->_hasSetting('level')) {
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
                        if ($component) {
                            $category = $component->getChildComponent('-' . $level);
                            if ($category && !Vpc_Abstract::getFlag($category->componentClass, 'menuCategory')) {
                                $category = false;
                            }
                        }
                    }
                } else if ($component->parent) {
                    $category = $component->parent->getChildComponent('-' . $level);
                } else {
                    $category = $component;
                }
                $ret = $category;
            } else {
                if (isset($currentPages[$level-2])) {
                    $ret = $currentPages[$level-2];
                }
            }
        }
        return $ret;
    }

    protected function _getMenuPages($parentData, $select)
    {
        if (is_array($select)) $select = new Vps_Component_Select($select);
        $select->whereShowInMenu(true);
        $ret = array();
        $pageComponent = $this->getPageComponent($parentData);
        if ($pageComponent) $ret = $pageComponent->getChildPages($select);
        return $ret;
    }

    protected function _getMenuData($parentData = null, $select = array())
    {
        $currentPageIds = array();
        $currentPages = array_reverse($this->_getCurrentPagesCached());
        foreach ($currentPages as $page) {
            if (!$page instanceof Vps_Component_Data_Root) {
                $currentPageIds[] = $page->getComponentId();
            }
        }
        $i = 0;
        $ret = array();
        $pages = $this->_getMenuPages($parentData, $select);
        foreach ($pages as $p) {
            $r = array(
                'data' => $p,
                'text' => $p->name
            );
            $class = array();
            if ($i == 0) { $class[] = 'first'; }
            if ($i == count($pages)-1) { $class[] = 'last'; }
            if (in_array($p->componentId, $currentPageIds)) {
                $class[] ='current';
                $r['current'] = true;
            }
            $cssClass = $this->_getConfig($p, 'cssClass');
            if ($cssClass) $class[] = $cssClass;
            $r['class'] = implode(' ', $class);
            $ret[] = $r;
            $i++;
        }
        return $ret;
    }

    protected function _getConfig($component, $key = null)
    {
        if (!$this->_getSetting('showAsEditComponent')) return null;
        $id = $component->componentId;
        if (!isset($this->_config[$id])) {
            $model = $this->_hasSetting('dataModel') ?
                Vps_Model_Abstract::getInstance($this->_getSetting('dataModel')) :
                null;
            $row = $model ? $model->getRow($id) : null;
            $this->_config[$id] = $row ? unserialize($row->data) : null;
        }
        $ret = $this->_config[$id];
        if ($key) {
            $ret = isset($ret[$key]) ? $ret[$key] : null;
        }
        return $ret;
    }

    // Array mit aktueller Seiten und Parent Pages
    protected final function _getCurrentPagesCached()
    {
        if (!isset($this->_currentPages)) {
            $this->_currentPages = $this->_getCurrentPages();
        }
        return $this->_currentPages;
    }

    protected function _getCurrentPages()
    {
        $ret = array();
        $p = $this->getData()->getPage();
        while ($p) {
            $ret[] = $p;
            $p = $p->getParentPage();
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (!isset($generator['showInMenu']) || !$generator['showInMenu']) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if (!$generator->getGeneratorFlag('page') || !$generator->getGeneratorFlag('table')) continue;
                $ret[] = new Vps_Component_Cache_Meta_Static_Model($generator->getModel());
            }
        }

        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Menu_Abstract_Model');

        return $ret;
    }
}
