<?php
class Vpc_Basic_ImageEnlarge_OriginalImageComponent extends Vpc_Basic_ImageEnlarge_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['fullSizeDownloadable'] = true;
        return $ret;
    }

}
