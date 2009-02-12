<?php
class Vps_Component_CacheVars_Box_Box extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vps_Component_CacheVars_Box_Model';
        return $ret;
    }
}
