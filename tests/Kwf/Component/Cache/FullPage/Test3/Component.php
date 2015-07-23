<?php
class Kwf_Component_Cache_FullPage_Test3_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_FullPage_Test3_Model';
        return $ret;
    }
}
