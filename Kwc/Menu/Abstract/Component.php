<?php
abstract class Kwc_Menu_Abstract_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Menu');
        $ret['componentIcon'] = 'layout';
        $ret['rootElementClass'] = 'kwfUp-webStandard kwfUp-webMenu kwfUp-webListNone';
        $ret['showParentPage'] = false;
        $ret['showParentPageLink'] = false;
        $ret['level'] = 'main';
        $ret['flags']['hasAlternativeComponent'] = true;

        $ret['plugins']['hideInvisibleDynamic'] = 'Kwc_Menu_Abstract_HideInvisibleDynamicPlugin';

        return $ret;
    }

    public function getActiveViewPlugins()
    {
        $ret = parent::getActiveViewPlugins();

        $found = false;
        $pages = $this->_getMenuPages(null, array());
        foreach ($pages as $p) {
            if (Kwc_Abstract::getFlag($p->componentClass, 'hasIsVisibleDynamic')) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            unset($ret['hideInvisibleDynamic']);
        }
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (isset($settings['showAsEditComponent'])) {
            throw new Kwf_Exception("showAsEditComponent setting doesn't exist anymore");
        }
        if ($settings['showParentPage'] || $settings['showParentPageLink']) {
            if (is_string($settings['level'])) {
                throw new Kwf_Exception("You can't use showParentPage for MainMenus (what should that do?)");
            }
        }
    }

    public static function getAlternativeComponents($componentClass)
    {
        if (!$componentClass) throw new Kwf_Exception("componentClass required");

        return array(

            //content is fetch from parent and slightly modified (current added etc)
            'parentMenu' => 'Kwc_Menu_ParentMenu_Component.'.$componentClass,

            //a category is shown but the current page is in another one. gets templateVars from the right component
            'otherCategory' => 'Kwc_Menu_OtherCategory_Component.'.$componentClass,

            //a category is shown but the current page is above categories. displays using ParentContent from the right child component
            'otherCategoryChild' => 'Kwc_Menu_OtherCategoryChild_Component.'.$componentClass,

            //if deep enough so no difference in menu content (depth is defined by _requiredLevels)
            'parentContent' => 'Kwc_Menu_ParentContent_Component.'.$componentClass,

            //if above categories
            'none' => 'Kwc_Basic_None_Component',
        );
    }

    protected static function _requiredLevels($componentClass)
    {
        $shownLevel = Kwc_Abstract::getSetting($componentClass, 'level');
        if (!is_numeric($shownLevel)) $shownLevel = 1;
        return $shownLevel;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        if ($generator instanceof Kwf_Component_Generator_Box_StaticSelect) {
            throw new Kwf_Exception("Menus are not compatible with StaticSelect");
        }
        if (!$parentData) return false;

        $foundPageOrCategory = false;
        $data = $parentData;
        do {
            if ($data->isPage || Kwc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
                $foundPageOrCategory = true;
                break;
            }
        } while ($data = $data->parent);
        if (!$foundPageOrCategory) {
            return 'otherCategoryChild';
        }

        $categoryData = $parentData;
        $menuLevel = 0;
        while ($categoryData && !Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory')) {
            if ($categoryData->isPage) $menuLevel++;
            $categoryData = $categoryData->parent;
        }

        $shownLevel = Kwc_Abstract::getSetting($componentClass, 'level');
        if (!is_numeric($shownLevel)) $shownLevel = 1;
        $requiredLevels = call_user_func(array($componentClass, '_requiredLevels'), $componentClass);
        $ret = false;
        if ($menuLevel > $requiredLevels) {
            $ret = 'parentContent';
        } else if ($shownLevel <= $menuLevel) {
            $ret = 'parentMenu';
            if (!is_numeric(Kwc_Abstract::getSetting($componentClass, 'level'))) {
                if ($categoryData) {
                    $cat = Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory');
                    if ($cat) {
                        if ($cat === true) $cat = $categoryData->id;
                        if ($cat != Kwc_Abstract::getSetting($componentClass, 'level')) {
                            //there are categories and we are in a different category than the menu is
                            //(so none is active and we can just show the parentContent (=efficient))
                            $ret = 'parentContent';
                        }
                    }
                } else {
                    //we are outside categories, show parentContent (until we reach otherCategoryChild)
                    $ret = 'parentContent';
                }
            }
        } else if ($menuLevel < $shownLevel-1) {
            $ret = 'none';
        }

        if ($ret == false) {
            if (!is_numeric(Kwc_Abstract::getSetting($componentClass, 'level'))) {
                $cat = Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory');
                if ($cat) {
                    if ($cat === true) $cat = $categoryData->id;
                    if ($cat != Kwc_Abstract::getSetting($componentClass, 'level')) {
                        $ret = 'otherCategory';
                    }
                } else {
                    //this would just display all pages (there are no categories)
                }
            }
        }

        //echo "$ret:: $parentData->componentId: menuLevel=$menuLevel requiredLevels=$requiredLevels shownLevel=$shownLevel level=".Kwc_Abstract::getSetting($componentClass, 'level')."\n";
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $ret['parentPage'] = null;
        $ret['parentPageLink'] = $this->_getSetting('showParentPageLink');
        if ($this->_getSetting('showParentPage') || $ret['parentPageLink']) {
            $ret['parentPage'] = $this->getData()->getPage();
        }
        return $ret;
    }

    /**
     * Used by chained and Events
     */
    public function getMenuData($parentData = null, $select = array(), $editableClass = 'Kwc_Menu_EditableItems_Component')
    {
        return $this->_getMenuData($parentData, $select, $editableClass);
    }

    protected function _getMenuPages($parentData, $select)
    {
        if (is_array($select)) $select = new Kwf_Component_Select($select);
        $select->whereShowInMenu(true);
        $ret = array();
        if ($parentData) {
            $pageComponent = $parentData;
        } else {
            $pageComponent = $this->getData();
            while ($pageComponent = $pageComponent->parent) {
                if ($pageComponent->isPage) break;
                if (Kwc_Abstract::getFlag($pageComponent->componentClass, 'menuCategory')) break;
            }
        }
        if ($pageComponent) $ret = $pageComponent->getChildPages($select);
        return $ret;
    }

    protected function _getMenuData($parentData = null, $select = array(), $editableClass = 'Kwc_Menu_EditableItems_Component')
    {
        $i = 0;
        $ret = array();
        $pages = $this->_getMenuPages($parentData, $select);
        foreach ($pages as $p) {
            $r = array(
                'data' => $p,
                'text' => '{name}',
                'preHtml' => '',
                'postHtml' => '',
                'last' => false
            );
            $class = array();
            $class[] = $this->_getBemClass('item');
            if ($i == 0) { $class[] = $this->_getBemClass('item--first', 'first'); }
            if ($i == count($pages)-1) {
                $class[] = $this->_getBemClass('item--last', 'last');
                $r['last'] = true;
            }
            if ($r['data']->getDeviceVisible() != Kwf_Component_Data::DEVICE_VISIBLE_ALL) {
                $class[] = $this->_getBemClass('item--'.$r['data']->getDeviceVisible(), $r['data']->getDeviceVisible());
            }

            $r['class'] = implode(' ', $class);
            if (Kwc_Abstract::getFlag($p->componentClass, 'hasIsVisibleDynamic')) {
                $r['preHtml'] = '<!-- start '.$p->componentId.' '.$p->componentClass.' -->';
                $r['postHtml'] = '<!-- end '.$p->componentId.' '.$p->componentClass.' -->';
            }
            foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
                $mask = $plugin->getMaskCode($p);
                $r['preHtml'] = $mask['begin'] . $r['preHtml'];
                $r['postHtml'] = $r['postHtml'] . $mask['end'];
            }
            $ret[] = $r;
            $i++;
        }
        $this->_attachEditableToMenuData($ret, $editableClass);

        return $ret;
    }

    protected function _attachEditableToMenuData(&$menuData, $editableClass)
    {
        foreach ($this->_getSetting('generators') as $key => $generator) {
            if (isset($generator['component'][$key]) && is_instance_of($generator['component'][$key], $editableClass)) {
                $c = $this->getData()->getChildComponent('-'.$key);
                $c->getComponent()->attachEditableToMenuData($menuData);
            }
        }
    }
}
