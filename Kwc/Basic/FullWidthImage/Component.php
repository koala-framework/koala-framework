<?php
class Kwc_Basic_FullWidthImage_Component extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Picture 100% width');
        $ret['componentCategory'] = 'media';
        $ret['componentPriority'] = 50;
        $ret['showHelpText'] = true;
        $ret['defineWidth'] = false;
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('full width'),
                'width' => self::CONTENT_WIDTH,
                'height' => 0,
                'cover' => true
            ),
            '16to9'=>array(
                'text' => trlKwfStatic('full width').' 16:9',
                'width' => self::CONTENT_WIDTH,
                'cover' => true,
                'aspectRatio' => 9/16
            ),
            '4to3'=>array(
                'text' => trlKwfStatic('full width').' 4:3',
                'width' => self::CONTENT_WIDTH,
                'cover' => true,
                'aspectRatio' => 3/4
            )
        );
        return $ret;
    }
}
