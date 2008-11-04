<?php
class Vpc_Box_DogearRandom_Dogear_ImageSmall_Component
    extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image small');
        $ret['dimensions'] = array(140, 140, Vps_Media_Image::SCALE_CROP);
        return $ret;
    }
}
