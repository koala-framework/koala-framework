<?php
/*
 * Doesn't extends Kwc_Menu_Abstract_Trl_Component like all other trl-menus.
 * Reason is that target menu can extend expandable-menu or menu but we must extend either one.
 * If this breaks something rethink and test it.
 */
class Kwc_Menu_OtherCategory_Trl_Component extends Kwc_Menu_OtherCategory_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $menuComponentClass = Kwc_Abstract::getSetting($masterComponentClass, 'menuComponentClass');
        $ret = parent::getSettings($menuComponentClass);
        $ret['masterComponentClass'] = $masterComponentClass;
        return $ret;
    }

    protected function _getMenuComponentClass()
    {
        return Kwc_Abstract::getSetting(
            $this->getData()->chained->componentClass, 'menuComponentClass'
        );
    }
}

