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
        $ret['level'] = 'main';
        $ret['flags']['hasAlternativeComponent'] = true;
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

        $data = $parentData;
        $menuLevel = 0;
        while ($data && !Vpc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
            if ($data->isPage) $menuLevel++;
            $data = $data->parent;
        }

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
                $cat = Vpc_Abstract::getFlag($data->componentClass, 'menuCategory');
                if ($cat) {
                    if ($cat === true) $cat = $data->id;
                    if ($cat != Vpc_Abstract::getSetting($componentClass, 'level')) {
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
                $cat = Vpc_Abstract::getFlag($parentData->componentClass, 'menuCategory');
                if ($cat) {
                    if ($cat === true) $cat = $parentData->id;
                    if ($cat != Vpc_Abstract::getSetting($componentClass, 'level')) {
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
     * Used by chained
     */
    public function getMenuData($parentData = null, $select = array())
    {
        return $this->_getMenuData($parentData, $select);
    }

    protected function _getMenuPages($parentData, $select)
    {
        if (is_array($select)) $select = new Vps_Component_Select($select);
        $select->whereShowInMenu(true);
        $ret = array();
        if ($parentData) {
            $pageComponent = $parentData;
        } else {
            $pageComponent = $this->getData()->parent;
        }
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
            $r['class'] = implode(' ', $class);
            $ret[] = $r;
            $i++;
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

        return $ret;
    }
}
