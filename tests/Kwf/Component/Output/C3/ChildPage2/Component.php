<?php
class Kwf_Component_Output_C3_ChildPage2_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentWidth'] = 600;
        return $ret;
    }
}
