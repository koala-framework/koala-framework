<?php
class Vpc_Basic_Image_FixDimensionComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(100, 100, Vps_Media_Image::SCALE_DEFORM);
        return $ret;
    }
}
