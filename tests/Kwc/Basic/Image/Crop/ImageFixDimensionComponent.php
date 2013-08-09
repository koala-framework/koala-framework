<?php
class Kwc_Basic_Image_Crop_ImageFixDimensionComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_Crop_TestModel';
        $ret['dimensions'] = array(
            array(
                'width' => 500,
                'height' => 500,
                'bestfit' => false,
            )
        );
        return $ret;
    }
}
