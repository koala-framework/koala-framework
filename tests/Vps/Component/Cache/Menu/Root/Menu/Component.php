<?php
class Vps_Component_Cache_Menu_Root_Menu_Component extends Vpc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        unset($ret['generators']['subMenu']);
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Menu_Abstract_Component::getTemplateVars();
        $ret['menu'] = $this->_getMenuData();
        return $ret;
    }
}
