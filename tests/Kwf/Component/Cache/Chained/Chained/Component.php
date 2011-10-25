<?php
class Kwf_Component_Cache_Chained_Chained_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
