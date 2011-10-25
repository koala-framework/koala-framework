<?php
class Kwc_Basic_ImageEnlarge_WithoutSmallImageComponent extends Kwc_Basic_ImageEnlarge_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTagWithoutSmall_TestComponent';
        return $ret;
    }
}
