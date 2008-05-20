<?php
/**
 * Menüdecorator. Speichert in Template-Variable alle Werte, die
 * für das Menü benötigt werden.
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Menu_Component extends Vpc_Menu_Abstract
{

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['menu'] = $this->_getMenuData();
        $return['level'] = $this->_getSetting('level');
        return $return;
    }
}
