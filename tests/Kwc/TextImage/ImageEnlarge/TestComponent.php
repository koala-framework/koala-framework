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
                'bestfit' => true,
            ),
            'small' => array(
                'text' => 'Small',
                'width' => 150,
                'height'=>null,
                'bestfit' => false,
            ),
            'original' => array(
                'text' => 'Original',
                'width'=>null,
                'height'=>null,
            ),
            'custom' => array(
                'text' => 'Custom',
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'bestfit' => true,
            )
        );
        return $ret;
    }

}
