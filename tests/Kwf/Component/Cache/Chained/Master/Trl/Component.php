<?php
class Kwf_Component_Cache_Chained_Master_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        return $ret;
    }
}
