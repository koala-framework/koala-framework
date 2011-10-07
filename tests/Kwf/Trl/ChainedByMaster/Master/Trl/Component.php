<?php
class Vps_Trl_ChainedByMaster_Master_Trl_Component extends Vpc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Vps_Trl_ChainedByMaster_Master_Trl_Model';
        return $ret;
    }
}
