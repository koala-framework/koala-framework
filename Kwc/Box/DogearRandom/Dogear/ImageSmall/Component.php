<?php
class Vpc_Box_DogearRandom_Dogear_ImageSmall_Component
    extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image small');
        $ret['dimensions'] = array(
            array('width'=>140, 'height'=>140, 'scale'=>Vps_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
