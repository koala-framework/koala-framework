<?php
class Kwc_List_Gallery_Image_Component extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image');
        $ret['imageCaption'] = true;

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_CROP,
                'aspectRatio' => 3/4,
            ),
        );
        return $ret;
    }
}
