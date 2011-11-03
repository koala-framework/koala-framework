<?php
class Kwf_Component_Cache_ProcessInput_WithProcessInput_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($data) {}
}