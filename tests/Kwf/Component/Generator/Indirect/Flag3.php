<?php
class Kwf_Component_Generator_Indirect_Flag3 extends Kwc_Abstract
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