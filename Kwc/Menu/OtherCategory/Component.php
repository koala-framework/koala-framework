<?php
class Kwc_Menu_OtherCategory_Component extends Kwc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $ret['plugins'] = Kwc_Abstract::getSetting($menuComponentClass, 'plugins');
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    //used by trl
    public function getMenuData()
    {
        return $this->_getMenuComponentData()->getComponent()->getMenuData();
    }

    private function _getMenuComponentData()
    {
        $category = Kwc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');
        $categoryData = $this->getData()->parent->parent->getChildComponent('-'.$category);
        $menu = $categoryData->getChildComponent('-'.$this->getData()->id);
        if (!is_instance_of($menu->componentClass, 'Kwc_Menu_Abstract_Component')) {
            throw new Kwf_Exception("got invalid menu component");
        }
        return $menu;
    }

    public function getTemplateVars()
    {
        $menu = $this->_getMenuComponentData();
        $ret = $menu->getComponent()->getTemplateVars();
        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);
        return $ret;
    }

    public function hasContent()
    {
        return $this->_getMenuComponentData()->hasContent();
    }
}
