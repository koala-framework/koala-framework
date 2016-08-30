<?php
class Kwf_Component_Cache_MasterHasContentBox_Box_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_MasterHasContentBox_Box_TestModel';
        return $ret;
    }
}