<?php
class Kwc_Basic_Image_CacheFullWidth_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true,
            ),
        );

        $ret['ownModel'] = 'Kwc_Basic_Image_CacheFullWidth_Image_TestModel';

        return $ret;
    }
}
