<?php
/**
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Menu_Dropdown_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $return['menu'] = $this->_getMenuData();
        foreach ($return['menu'] as $m) {
            $m->submenu = $this->_getMenuData($m);
        }
        $return['level'] = $this->_getSetting('level');
        
        return $return;
    }
}
