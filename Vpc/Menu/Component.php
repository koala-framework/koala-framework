<?php
/**
 * Menübox. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 */
class Vpc_Menu_Component extends Vpc_Menu_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['maxLevel'] = 1;
        $ret['childComponentClasses']['subMenu'] = 'Vpc_Menu_Component';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['menu'] = $this->_getMenuData();
        $ret['level'] = $this->_getSetting('level');
        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');
        return $ret;
    }
}
