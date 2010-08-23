<?php
class Vpc_Cc_RootWithTrl_Master_Master_Category_Trl_Component extends Vpc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Vpc_Cc_RootWithTrl_Master_Master_Category_Trl_Model';
        return $ret;
    }
}