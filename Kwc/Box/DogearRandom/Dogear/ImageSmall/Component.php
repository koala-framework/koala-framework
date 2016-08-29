<?php
class Kwc_Box_DogearRandom_Dogear_ImageSmall_Component
    extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Image small');
        $ret['dimensions'] = array(
            array('width'=>140, 'height'=>140, 'cover' => true)
        );
        return $ret;
    }
}
