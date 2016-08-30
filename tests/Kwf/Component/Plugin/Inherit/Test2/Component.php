<?php
class Kwf_Component_Plugin_Inherit_Test2_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pluginsInherit'][] = 'Kwf_Component_Plugin_Inherit_Test2_Plugin';
        return $ret;
    }
}
