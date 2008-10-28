<?php
class Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent extends Vpc_Basic_ImageEnlarge_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['hasSmallImageComponent'] = false;
        return $ret;
    }
}
