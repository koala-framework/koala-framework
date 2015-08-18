<?php
class Kwc_Cc_RootWithTrl_Master_Master_Category_Trl_Component extends Kwc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Kwc_Cc_RootWithTrl_Master_Master_Category_Trl_Model';
        $ret['generators']['page']['historyModel'] = 'Kwc_Cc_RootWithTrl_Master_Master_Category_Trl_HistoryModel';
        return $ret;
    }
}