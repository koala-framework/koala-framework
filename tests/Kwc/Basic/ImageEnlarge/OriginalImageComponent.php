<?php
class Kwc_Basic_ImageEnlarge_OriginalImageComponent extends Kwc_Basic_ImageEnlarge_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTagWithOriginal_TestComponent';
        return $ret;
    }

}
