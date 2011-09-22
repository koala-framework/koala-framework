<?php
class Vpc_Menu_OtherCategory_Component extends Vpc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    public function getTemplateVars()
    {
        $category = Vpc_Abstract::getSetting($this->_getSetting('menuComponentClass'), 'level');
        $categoryData = $this->getData()->parent->parent->getChildComponent('-'.$category);
        $menu = $categoryData->getChildComponent('-'.$this->getData()->id);
        if (!is_instance_of($menu->componentClass, 'Vpc_Menu_Abstract_Component')) {
            throw new Vps_Exception("got invalid menu component");
        }

        $ret = $menu->getComponent()->getTemplateVars();

        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);

        return $ret;
    }
}
