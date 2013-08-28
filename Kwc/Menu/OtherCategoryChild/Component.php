<?php
class Kwc_Menu_OtherCategoryChild_Component extends Kwc_Basic_ParentContent_Component
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = Kwc_Abstract::getSetting($menuComponentClass, 'viewCache');
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    protected function _getParentContentData()
    {
        $category = Kwc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');
        $categoryData = $this->getData()->parent->getChildComponent('-'.$category);
        $menu = $categoryData->getChildComponent('-'.$this->getData()->id);
        if (!is_instance_of($menu->componentClass, 'Kwc_Menu_Abstract_Component')) {
            throw new Kwf_Exception("got invalid menu component");
        }
        return $menu;
    }
}
