<?php
/**
 * @package Vpc
 * @subpackage Decorator
 */
class Vpc_Menu_Dropdown_Component extends Vpc_Menu_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['menu'] = $this->_getMenuData();
        foreach ($ret['menu'] as $m) {
            $m->submenu = $this->_getMenuData($m);
        }
        $ret['level'] = $this->_getSetting('level');
        
        return $ret;
    }
}
