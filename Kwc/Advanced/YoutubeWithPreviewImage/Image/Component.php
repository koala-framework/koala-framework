<?php
class Kwc_Advanced_YoutubeWithPreviewImage_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'fullwidth'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            ),
        );
        return $ret;
    }
}
