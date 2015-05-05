<?php
class Kwf_Trl_ChainedByMaster_Chained_Component extends Kwc_Root_TrlRoot_Chained_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['class'] = 'Kwc_Root_Category_Trl_Generator';
        $ret['generators']['page']['model'] = 'Kwf_Trl_ChainedByMaster_Chained_Model';
        return $ret;
    }
}
