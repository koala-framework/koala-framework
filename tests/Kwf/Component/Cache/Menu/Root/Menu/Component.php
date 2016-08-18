<?php
class Kwf_Component_Cache_Menu_Root_Menu_Component extends Kwc_Menu_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['level'] = 'root';
        unset($ret['generators']['subMenu']);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = Kwc_Menu_Abstract_Component::getTemplateVars($renderer);
        $ret['menu'] = $this->_getMenuData();
        return $ret;
    }
}
