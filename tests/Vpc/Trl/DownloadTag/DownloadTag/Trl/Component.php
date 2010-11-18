<?php
class Vpc_Trl_DownloadTag_DownloadTag_Trl_Component extends Vpc_Basic_DownloadTag_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vpc_Trl_DownloadTag_DownloadTag_Trl_TestModel';
        return $ret;
    }
}
