<?php
class Kwc_Trl_DownloadTag_DownloadTag_Trl_Component extends Kwc_Basic_DownloadTag_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_DownloadTag_DownloadTag_Trl_TestModel';
        return $ret;
    }
}
