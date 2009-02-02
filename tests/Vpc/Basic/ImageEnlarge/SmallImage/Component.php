<?php
class Vpc_Basic_ImageEnlarge_SmallImage_Component extends Vpc_Basic_Image_Thumb_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(10, 10, Vps_Media_Image::SCALE_DEFORM);
        $ret['modelname'] = 'Vpc_Basic_ImageEnlarge_SmallImage_TestModel';
        return $ret;
    }
}
