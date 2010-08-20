<?php
class Vps_Cc_Slave_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc');
        $ret['chainedType'] = 'Cc';
        return $ret;
    }
}