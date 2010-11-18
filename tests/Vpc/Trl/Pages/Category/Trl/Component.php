<?php
class Vpc_Trl_Pages_Category_Trl_Component extends Vpc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Vpc_Trl_Pages_Category_Trl_PagesTrlTestModel';
        return $ret;
    }
}
