<?php
class Vpc_Test_Component_Flag_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        return $ret;
    }
}
?>