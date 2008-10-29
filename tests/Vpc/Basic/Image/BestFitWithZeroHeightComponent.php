<?php
class tests_Vpc_Basic_Image_BestFitWithZeroHeightComponent extends Vpc_Basic_Image_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(100, 0, Vps_Media_Image::SCALE_BESTFIT);
        return $ret;
    }

}
