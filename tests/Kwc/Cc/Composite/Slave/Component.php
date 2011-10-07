<?php
class Kwc_Cc_Composite_Slave_Component extends Kwc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc');
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}
