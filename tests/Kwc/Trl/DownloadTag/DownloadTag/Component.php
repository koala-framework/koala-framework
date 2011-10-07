<?php
class Vpc_Trl_DownloadTag_DownloadTag_Component extends Vpc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_DownloadTag_DownloadTag_TestModel';
        return $ret;
    }
}
