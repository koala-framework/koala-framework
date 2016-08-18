<?php
class Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Component extends Kwc_Basic_DownloadTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Plugin_Inherit_Test1_DownloadTag_Model';
        return $ret;
    }
}
