<?php
class Vpc_Box_DogearRandom_Dogear_Image_Component
    extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image big');
        $ret['dimensions'] = array(640, 640, Vps_Media_Image::SCALE_CROP);
        return $ret;
    }
}
