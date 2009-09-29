<?php
class Vpc_Basic_Text_Download_TestComponent extends Vpc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Text_Download_TestModel';
        return $ret;
    }

}
