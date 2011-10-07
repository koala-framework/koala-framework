<?php
class Vps_Assets_WithComponents_Component1_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/tests/Vps/Assets/OwnConfig/file2.js';
        return $ret;
    }
}
