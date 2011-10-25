<?php
class Kwf_Assets_WithComponents_Component1_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/tests/Kwf/Assets/OwnConfig/file2.js';
        return $ret;
    }
}
