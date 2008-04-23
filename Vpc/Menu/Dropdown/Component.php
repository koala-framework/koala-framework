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

        $return['menu'] = array();
        foreach ($this->_getMenuData() as $m) {
            $m['submenu'] = $this->_getMenuData($m['componentId']);
            $return['menu'][] = $m;
        }
        $return['level'] = $this->_getSetting('level');
        
        return $return;
    }
}
