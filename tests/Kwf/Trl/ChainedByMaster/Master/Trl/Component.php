<?php
class Kwf_Trl_ChainedByMaster_Master_Trl_Component extends Kwc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Kwf_Trl_ChainedByMaster_Master_Trl_Model';
        return $ret;
    }
}
