<?php
class Kwc_Basic_Image_BestFitWithZeroHeightComponent extends Kwc_Basic_Image_TestComponent
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(array('width'=>16, 'height'=>null, 'cover' => true));
        return $ret;
    }

}
