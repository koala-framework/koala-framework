<?php
class Kwc_TextImage_ImageEnlarge_TestComponent extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_ImageEnlarge_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_TextImage_ImageEnlarge_LinkTag_TestComponent';
        $ret['dimensions'] =  array(
            'large' => array(
                'text' => 'groß auf der Seite',
                'width' => 300,
                'height' => 200,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            ),
            'small' => array(
                'text' => 'Small',
                'width' => 150,
                'height'=>null,
                'scale' => Kwf_Media_Image::SCALE_DEFORM
            ),
            'original' => array(
                'text' => 'Original',
                'width'=>null,
                'height'=>null,
                'scale' => Kwf_Media_Image::SCALE_ORIGINAL
            ),
            'custom' => array(
                'text' => 'Custom',
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            )
        );
        return $ret;
    }

}
