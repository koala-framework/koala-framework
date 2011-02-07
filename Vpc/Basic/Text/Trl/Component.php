<?php
class Vpc_Basic_Text_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Text/Trl/CopyButton.js';
        $ret['editComponents'] = array();
        return $ret;
    }
}
