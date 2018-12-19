<?php
class Kwc_Menu_OtherCategory_Component extends Kwc_Abstract
{
    public static function getSettings($menuComponentClass = null)
    {
        $ret = parent::getSettings($menuComponentClass);
        $ret['plugins'] = Kwc_Abstract::getSetting($menuComponentClass, 'plugins');
        $ret['viewCache'] = Kwc_Abstract::getSetting($menuComponentClass, 'viewCache');
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    public function getActiveViewPlugins()
    {
        return $this->_getMenuSource()->getComponent()->getActiveViewPlugins();
    }

    //used by trl
    public function getMenuData()
    {
        return $this->_getMenuSource()->getComponent()->getMenuData();
    }

    private function _getMenuSource()
    {
        $category = Kwc_Abstract::getSetting($this->_getMenuComponentClass(), 'level');

        // get first parent with boxes as menu can also be below composite
        $firstParentWithMenuBoxes = $this->getData()->getPage();
        if (!$firstParentWithMenuBoxes) {
            $firstParentWithMenuBoxes = $this->getData()->getSubroot();
        }

        // get path from box-component to actual menu-component deeper inside the box-component
        $menuComponentIdPath = array();
        $menuComponentIdPath[] = $this->getData()->id;
        $ownBoxStartCmp = $this->getData();

        while ($ownBoxStartCmp->parent != $firstParentWithMenuBoxes
            && !Kwc_Abstract::getFlag($ownBoxStartCmp->parent->componentClass, 'menuCategory')
        ) {
            $ownBoxStartCmp = $ownBoxStartCmp->parent;
            $menuComponentIdPath[] = $ownBoxStartCmp->id;
        }
        $menuComponentIdPath = array_reverse($menuComponentIdPath);

        // get default menu-component
        $menu = $firstParentWithMenuBoxes->getChildComponent('-'.$category);
        foreach ($menuComponentIdPath as $componentId) {
            $menu = $menu->getChildComponent('-'.$componentId);
        }
        return $menu;
    }

    // overwritten by trl
    protected function _getMenuComponentClass()
    {
        return $this->_getSetting('menuComponentClass');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $menu = $this->_getMenuSource();
        $ret = $menu->getComponent()->getTemplateVars($renderer);
        while (isset($menu->chained)) $menu = $menu->chained;
        $ret['template'] = self::getTemplateFile($menu->componentClass);
        return $ret;
    }

    public function hasContent()
    {
        return $this->_getMenuSource()->hasContent();
    }
}
