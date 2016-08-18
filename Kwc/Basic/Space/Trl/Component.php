<?php
class Kwc_Basic_Space_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        return $ret;
    }
}
