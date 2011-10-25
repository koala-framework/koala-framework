<?php
class Kwc_Basic_Image_FixDimensionComponent extends Kwc_Basic_Image_Component
{
    public static $getMediaOutputCalled = 0;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['dimensions'] = array(array(
            'width' => 100, 'height' => 100, 'scale' => Kwf_Media_Image::SCALE_DEFORM));
        $ret['editFilename'] = true;
        return $ret;
    }
    public static function getMediaOutput($id, $type, $className)
    {
        self::$getMediaOutputCalled++;
        return parent::getMediaOutput($id, $type, $className);
    }
}
