<?php
class Kwc_List_Carousel_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Carousel');
        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 337,
                'cover' => true
            ),
        );
        $ret['defineWidth'] = true;
        return $ret;
    }
}
