<?php
class Kwc_Basic_Image_CacheSimple_ImageComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        return $ret;
    }
}
