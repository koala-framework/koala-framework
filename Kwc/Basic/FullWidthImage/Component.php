<?php
class Kwc_Basic_FullWidthImage_Component extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Picture 100% width');
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 50;
        $ret['showHelpText'] = true;
        $ret['defineWidth'] = false;
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
