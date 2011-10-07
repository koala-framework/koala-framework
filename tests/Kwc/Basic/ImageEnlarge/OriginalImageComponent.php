<?php
class Vpc_Basic_ImageEnlarge_OriginalImageComponent extends Vpc_Basic_ImageEnlarge_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Basic_ImageEnlarge_EnlargeTagWithOriginal_TestComponent';
        return $ret;
    }

}
