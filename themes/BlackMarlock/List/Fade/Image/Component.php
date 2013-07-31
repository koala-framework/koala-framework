<?php
class BlackMarlock_List_Fade_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image');

        $ret['dimensions'] = array(
            'customcrop'=>array(
                'text' => trlKwfStatic('user-defined'),
                'width' => 910,
                'height' => 350,
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}
