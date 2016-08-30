<?php
class Kwf_Component_Cache_Box_IcRoot_InheritContent_Component extends Kwc_Box_InheritContent_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwf_Component_Cache_Box_IcRoot_InheritContent_Child_Component';
        return $ret;
    }
}
