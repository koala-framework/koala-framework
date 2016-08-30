<?php
class Kwc_Menu_OtherCategoryChild_Trl_Component extends Kwc_Basic_ParentContent_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($param);
        $ret['plugins'] = Kwc_Abstract::getSetting($masterComponentClass, 'plugins');
        $ret['viewCache'] = Kwc_Abstract::getSetting($masterComponentClass, 'viewCache');
        $ret['menuComponentClass'] = Kwc_Abstract::getSetting($masterComponentClass, 'menuComponentClass');
        return $ret;
    }

    protected function _getParentContentData()
    {
        $category = Kwc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');
        $categoryData = $this->getData()->parent->getChildComponent('-'.$category);
        return $categoryData->getChildComponent('-'.$this->getData()->id);
    }
}
