<?php
class Vpc_TextImage_ImageEnlarge_TestComponent extends Vpc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_TextImage_ImageEnlarge_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_TestComponent';
        $ret['dimensions'] =  array(
            'large' => array(
                'text' => 'groß auf der Seite',
                'width' => 300,
                'height' => 200,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            ),
            'small' => array(
                'text' => 'Small',
                'width' => 150,
                'height'=>null,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'original' => array(
                'text' => 'Original',
                'width'=>null,
                'height'=>null,
                'scale' => Vps_Media_Image::SCALE_ORIGINAL
            ),
            'custom' => array(
                'text' => 'Custom',
                'width' => Vpc_Abstract_Image_Component::USER_SELECT,
                'height' => Vpc_Abstract_Image_Component::USER_SELECT,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            )
        );
        return $ret;
    }

}
