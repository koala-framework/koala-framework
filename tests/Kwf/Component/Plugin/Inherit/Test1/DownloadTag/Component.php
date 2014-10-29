<?php
class Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component extends Kwc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Model';
        return $ret;
    }
}
