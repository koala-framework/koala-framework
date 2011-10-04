<?php
class Vps_Component_Cache_Box_IcRoot_InheritContent_Component extends Vpc_Box_InheritContent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vps_Component_Cache_Box_IcRoot_InheritContent_Child_Component';
        return $ret;
    }
}
