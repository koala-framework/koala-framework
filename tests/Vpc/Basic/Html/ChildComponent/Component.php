<?php
class Vpc_Basic_Html_ChildComponent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['model'] = new Vps_Model_FnF();
        return $ret;
    }

}
