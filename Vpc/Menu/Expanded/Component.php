<?php
class Vpc_Menu_Expanded_Component extends Vpc_Menu_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['menuModel'] = 'Vpc_Menu_Expanded_MenuModel';
        return $ret;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $level = self::_getMenuLevel($componentClass, $parentData, $generator);
        $maxLevel = (int)Vpc_Abstract::getSetting($componentClass, 'level');
        return $level > ($maxLevel + 2);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['menu'] = $this->_getMenuData();
        foreach ($ret['menu'] as $k=>$m) {
            $ret['menu'][$k]['submenu'] = $this->_getMenuData($m['data']);
        }
        $ret['level'] = $this->_getSetting('level');

        return $ret;
    }

    public function hasContent()
    {
        $c = count($this->_getMenuData());
        if ($c > 0) {
            return true;
        }
        return false;
    }

}
