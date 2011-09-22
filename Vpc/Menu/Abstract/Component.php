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
        $ret['showParentPageLink'] = false;
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Menu/Abstract/Panel.js';
        $ret['showAsEditComponent'] = false;
        $ret['liCssClasses'] = array(
            'offset' => trlVps('Offset')
        );
        $ret['level'] = 'main';
        $ret['dataModel'] = 'Vpc_Menu_Abstract_Model';
        $ret['menuModel'] = 'Vpc_Menu_Abstract_MenuModel';
        $ret['flags']['hasAlternativeComponent'] = true;

        //$ret['extConfig'] = 'Vpc_Menu_Abstract_ExtConfig';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        /*
        if (isset($settings['showAsEditComponent'])) {
            throw new Vps_Exception("showAsEditComponent setting doesn't exist anymore");
        }
        */

        if ($settings['showParentPage'] || $settings['showParentPageLink']) {
            if (is_string($settings['level'])) {
                throw new Vps_Exception("You can't use showParentPage for MainMenus (what should that do?)");
            }
        }
    }

    public static function getAlternativeComponents($componentClass)
    {
        if (!$componentClass) throw new Vps_Exception("componentClass required");

        return array(

            //content is fetch from parent and slightly modified (current added etc)
            'parentMenu' => 'Vpc_Menu_ParentMenu_Component.'.$componentClass,

            //a category is shown but the current page is in another one. gets templateVars from the right component
            'otherCategory' => 'Vpc_Menu_OtherCategory_Component.'.$componentClass,

            //if deep enough so no difference in menu content (depth is defined by _requiredLevels)
            'parentContent' => 'Vpc_Basic_ParentContent_Component',

            //if above categories
            'empty' => 'Vpc_Basic_Empty_Component',
        );
    }

    protected static function _requiredLevels($componentClass)
    {
        $shownLevel = Vpc_Abstract::getSetting($componentClass, 'level');
        if (!is_numeric($shownLevel)) $shownLevel = 1;
        return $shownLevel;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $foundPageOrCategory = false;
        $data = $parentData;
        do {
            if ($data->isPage || Vpc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
                $foundPageOrCategory = true;
                break;
            }
        } while ($data = $data->parent);
        if (!$foundPageOrCategory) return 'empty';

        $menuLevel = self::_getMenuLevel($componentClass, $parentData, $generator);
        $shownLevel = Vpc_Abstract::getSetting($componentClass, 'level');
        if (!is_numeric($shownLevel)) $shownLevel = 1;
        $requiredLevels = call_user_func(array($componentClass, '_requiredLevels'), $componentClass);

        $ret = false;
        if ($menuLevel > $requiredLevels) {
            $ret = 'parentContent';
        } else if ($shownLevel <= $menuLevel) {
            $ret = 'parentMenu';
            if (!is_numeric(Vpc_Abstract::getSetting($componentClass, 'level'))) {
                $data = $parentData;
                do {
                    if (Vpc_Abstract::getFlag($data->componentClass, 'menuCategory')) break;
                } while ($data = $data->parent);
                if (Vpc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
                    if ($data->id != Vpc_Abstract::getSetting($componentClass, 'level')) {
                        //there are categories and we are in a different category than the menu is
                        //(so none is active and we can just show the parentContent (=efficient))
                        $ret = 'parentContent';
                    }
                }
            }
        } else if ($menuLevel < $shownLevel-1) {
            return 'empty';
        }

        if ($ret == false) {
            if (!is_numeric(Vpc_Abstract::getSetting($componentClass, 'level'))) {
                if (Vpc_Abstract::getFlag($parentData->componentClass, 'menuCategory')) {
                    if ($parentData->id != Vpc_Abstract::getSetting($componentClass, 'level')) {
                        $ret = 'otherCategory';
                    }
                } else {
                    //this would just display all pages (there are no categories)
                }
            }
        }

        //echo "$ret:: $parentData->componentId: menuLevel=$menuLevel requiredLevels=$requiredLevels shownLevel=$shownLevel level=".Vpc_Abstract::getSetting($componentClass, 'level')."\n";
        return $ret;
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
        $ret['parentPageLink'] = $this->_getSetting('showParentPageLink');
        if ($this->_getSetting('showParentPage') || $ret['parentPageLink']) {
            $ret['parentPage'] = $this->getData()->getPage();
        }
        return $ret;
    }

    /**
     * Used in PagesController
     */
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

    /**
     * Returns the data whose child pages will be displayed in that menu
     *
     * TODO: remove parentData parameter, that doesn't make any sense
     */
    public function getPageComponent($parentData = null)
    {
        if ($parentData) {
            $ret = $parentData;
        } else {
            $level = $this->_getSetting('level');
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
                $ret = $this->getData()->getPage();
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
