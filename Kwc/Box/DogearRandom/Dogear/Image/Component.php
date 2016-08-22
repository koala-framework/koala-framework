<?php
class Kwc_Box_DogearRandom_Dogear_Image_Component
    extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Image big');
        $ret['dimensions'] = array(
            array('width'=>640, 'height'=>640, 'cover' => true)
        );
        return $ret;
    }
}
