<?php
class Kwc_Box_DogearRandom_Dogear_ImageSmall_Component
    extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image small');
        $ret['dimensions'] = array(
            array('width'=>140, 'height'=>140, 'scale'=>Kwf_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
