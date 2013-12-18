<?php
class Kwc_Basic_FullWidthImage_Component extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlStatic('Picture 100% width');
        $ret['showHelpText'] = true;
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            )
        );
        return $ret;
    }
}
