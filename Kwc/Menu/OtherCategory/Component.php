<?php
class Kwc_Menu_OtherCategory_Component extends Kwc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    public function getTemplateVars()
    {
        $category = Kwc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');
        $categoryData = $this->getData()->parent->parent->getChildComponent('-'.$category);
        $menu = $categoryData->getChildComponent('-'.$this->getData()->id);
        if (!is_instance_of($menu->componentClass, 'Kwc_Menu_Abstract_Component')) {
            throw new Kwf_Exception("got invalid menu component");
        }

        $ret = $menu->getComponent()->getTemplateVars();

        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);

        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $c = Kwc_Abstract::getSetting($componentClass, 'menuComponentClass');
        return call_user_func(array($c, 'getStaticCacheMeta'), $c);
        return $ret;
    }
}
