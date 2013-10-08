<?php
class Kwc_Basic_Image_Crop_ImageFixDimensionComponent extends Kwc_Basic_Image_Component
{
    public static $getMediaOutputCalled = 0;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_Crop_TestModel';
        $ret['dimensions'] = array(
            array(
                'width' => 500,
                'height' => 500,
                'cover' => true,
            )
        );
        return $ret;
    }
    public static function getMediaOutput($id, $type, $className)
    {
        self::$getMediaOutputCalled++;
        return parent::getMediaOutput($id, $type, $className);
    }
}
