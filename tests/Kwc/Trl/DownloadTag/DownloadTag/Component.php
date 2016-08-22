<?php
class Kwc_Trl_DownloadTag_DownloadTag_Component extends Kwc_Basic_DownloadTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_DownloadTag_DownloadTag_TestModel';
        return $ret;
    }
}
