<?php
class Kwc_Basic_BackgroundWindowWidth_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        return $ret;
    }
}
