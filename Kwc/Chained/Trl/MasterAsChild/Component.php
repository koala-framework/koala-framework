<?php
class Vpc_Chained_Trl_MasterAsChild_Component extends Vpc_Chained_Abstract_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Vpc_Chained_Trl_MasterAsChild_ContentSender';
        return $ret;
    }
}
