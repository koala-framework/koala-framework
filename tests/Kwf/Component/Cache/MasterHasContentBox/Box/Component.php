<?php
class Kwf_Component_Cache_MasterHasContentBox_Box_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_MasterHasContentBox_Box_TestModel';
        return $ret;
    }
}