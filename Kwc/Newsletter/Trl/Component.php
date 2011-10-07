<?php
class Vpc_Newsletter_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasResources'] = true;
        return $ret;
    }
}
