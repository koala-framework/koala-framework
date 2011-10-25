<?php
class Kwf_Component_Generator_Recursive_Flag extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        return $ret;
    }

}
