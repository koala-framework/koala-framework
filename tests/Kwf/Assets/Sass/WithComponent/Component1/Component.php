<?php
class Kwf_Assets_Sass_WithComponent_Component1_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/tests/Kwf/Assets/Sass/OwnConfig/file2.scss';
        return $ret;
    }
}
