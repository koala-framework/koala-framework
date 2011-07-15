<?php
class Vpc_List_Gallery_Image_Component extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Bild');
        $ret['generators']['child']['component']['linkTag'] =
            'Vpc_List_Gallery_Image_LinkTag_Component';
        $ret['imageCaption'] = true;

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlVps('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
        );
        return $ret;
    }
}
