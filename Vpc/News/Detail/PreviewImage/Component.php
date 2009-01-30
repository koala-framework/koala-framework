<?php
class Vpc_News_Detail_PreviewImage_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            array(
                'width'=>30,
                'height'=>20,
                'scale'=>Vps_Media_Image::SCALE_BESTFIT
            )
        );
        return $ret;
    }
}
