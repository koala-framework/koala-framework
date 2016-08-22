<?php
class Kwf_Component_Cache_Composite_Root_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['c1'] = 'Kwf_Component_Cache_Composite_Root_C1_Component';
        return $ret;
    }
}
