<?php
class Kwf_Component_Cache_ProcessInput_WithProcessInput_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($data) {}
}