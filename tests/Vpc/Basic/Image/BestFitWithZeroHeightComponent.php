<?php
class Vpc_Basic_Image_BestFitWithZeroHeightComponent extends Vpc_Basic_Image_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(array('width'=>100, 'height'=>null, 'scale'=>Vps_Media_Image::SCALE_DEFORM));
        return $ret;
    }

}
