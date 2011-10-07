<?php
class Vpc_Cc_Composite_Slave_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc');
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}
