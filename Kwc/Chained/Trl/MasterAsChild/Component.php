<?php
class Kwc_Chained_Trl_MasterAsChild_Component extends Kwc_Chained_Abstract_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Kwc_Chained_Trl_MasterAsChild_ContentSender';
        return $ret;
    }
}
