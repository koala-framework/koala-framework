<?php
class Vps_Component_Generator_Indirect_Flag3 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        $ret['flags']['bar'] = true;
        $ret['flags']['foobar'] = true;
        return $ret;
    }
}
?>