<?php
class Kwf_Component_PluginRoot_MaskComponentLink_Menu_Component extends Kwc_Menu_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['level'] = 'root';
        unset($ret['generators']['subMenu']);
        return $ret;
    }
}
