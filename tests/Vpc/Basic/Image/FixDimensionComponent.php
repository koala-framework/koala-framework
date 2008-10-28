<?php
class Vpc_Basic_Image_FixDimensionComponent extends Vpc_Basic_Image_Component
{
    public static $getMediaOutputCalled = 0;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(100, 100, Vps_Media_Image::SCALE_DEFORM);
        return $ret;
    }
    public static function getMediaOutput($id, $type, $className)
    {
        self::$getMediaOutputCalled++;
        return parent::getMediaOutput($id, $type, $className);
    }
}
