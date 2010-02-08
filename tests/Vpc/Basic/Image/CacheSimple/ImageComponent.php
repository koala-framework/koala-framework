<?php
class Vpc_Basic_Image_CacheSimple_ImageComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        return $ret;
    }
}
