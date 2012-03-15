<?php
class Kwc_List_Gallery_Image_Component extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Image');
        $ret['generators']['child']['component']['linkTag'] =
            'Kwc_List_Gallery_Image_LinkTag_Component';
        $ret['imageCaption'] = true;

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_DEFORM,
                'aspectRatio' => false, //eg. 3/4 in combination with SCALE_CROP
            ),
        );
        return $ret;
    }
}
