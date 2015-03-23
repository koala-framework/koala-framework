<?php
class Kwc_Trl_Domains_Domain_Master_Category_Trl_Component extends Kwc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Kwc_Trl_Domains_Domain_Master_Category_Trl_PagesTrlTestModel';
        return $ret;
    }
}
