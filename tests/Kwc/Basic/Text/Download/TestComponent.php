<?php
class Kwc_Basic_Text_Download_TestComponent extends Kwc_Basic_DownloadTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Text_Download_TestModel';
        return $ret;
    }

}
