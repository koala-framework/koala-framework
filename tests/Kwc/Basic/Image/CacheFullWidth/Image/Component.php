<?php
class Kwc_Basic_Image_CacheFullWidth_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['dimensions'] = array(
            'fullWidth'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'bestfit' => false,
            ),
        );

        $ret['ownModel'] = 'Kwc_Basic_Image_CacheFullWidth_Image_TestModel';

        return $ret;
    }
}
