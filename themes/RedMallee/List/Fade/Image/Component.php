<?php
class RedMallee_List_Fade_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image');

        $ret['dimensions'] = array(
            'customcrop'=>array(
                'text' => trlKwfStatic('user-defined'),
                'width' => 980,
                'height' => 185,
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}
