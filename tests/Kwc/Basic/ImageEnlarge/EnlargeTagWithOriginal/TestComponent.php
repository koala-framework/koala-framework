<?php
class Kwc_Basic_ImageEnlarge_EnlargeTagWithOriginal_TestComponent extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_ImageEnlarge_TestModel';
        $ret['fullSizeDownloadable'] = true;
        return $ret;
    }
}
