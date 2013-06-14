<?php
class Default_Box_Logo_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlStatic('Logo');
        $ret['dimensions'] = array(
            'customcrop'=>array(
                'text' => trlKwf('user-defined'),
                'width' => 180,
                'height' => 100,
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}
