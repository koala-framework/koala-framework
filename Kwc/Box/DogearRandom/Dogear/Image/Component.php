<?php
class Kwc_Box_DogearRandom_Dogear_Image_Component
    extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image big');
        $ret['dimensions'] = array(
            array('width'=>640, 'height'=>640, 'scale'=>Kwf_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
