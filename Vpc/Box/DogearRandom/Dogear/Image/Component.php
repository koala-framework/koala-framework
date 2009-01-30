<?php
class Vpc_Box_DogearRandom_Dogear_Image_Component
    extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image big');
        $ret['dimensions'] = array(
            array('width'=>640, 'height'=>640, 'scale'=>Vps_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
