<?php
class Kwc_Menu_OtherCategory_Component extends Kwc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
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
        $categoryData = $this->getData()->parent->parent->getChildComponent('-'.$category);
        $menu = $categoryData->getChildComponent('-'.$this->getData()->id);
        return $menu;
    }

    // overwritten by trl
    protected function _getMenuComponentClass()
    {
        return $this->_getSetting('menuComponentClass');
    }

    public function getTemplateVars()
    {
        $menu = $this->_getMenuSource();
        $ret = $menu->getComponent()->getTemplateVars();
        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);
        return $ret;
    }

    public function hasContent()
    {
        return $this->_getMenuSource()->hasContent();
    }
}
