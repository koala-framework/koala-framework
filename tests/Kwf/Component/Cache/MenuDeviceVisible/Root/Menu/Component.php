<?php
class Kwf_Component_Cache_MenuDeviceVisible_Root_Menu_Component extends Kwc_Menu_Component
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
        $ret = Kwc_Menu_Abstract_Component::getTemplateVars();
        $ret['menu'] = $this->_getMenuData();
        return $ret;
    }
}
