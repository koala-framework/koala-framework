<?php
/*
 * Doesn't extends Kwc_Menu_Abstract_Trl_Component like all other trl-menus.
 * Reason is that target menu can extend expandable-menu or menu but we must extend either one.
 * If this breaks something rethink and test it.
 */
class Kwc_Menu_OtherCategory_Trl_Component extends Kwc_Menu_OtherCategory_Component
{
    protected function _getMenuComponentClass()
    {
        return Kwc_Abstract::getSetting(
            $this->getData()->chained->componentClass, 'menuComponentClass'
        );
    }
}

