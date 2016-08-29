<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_Component extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_Component';
        $ret['dimensions'] =  array(
            'large' => array(
                'text' => 'groß auf der Seite',
                'width' => 300,
                'height' => 200,
                'cover' => true,
            ),
            'small' => array(
                'text' => 'Small',
                'width' => 150,
                'height'=>null,
                'cover' => true,
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
                'cover' => false,
            )
        );
        return $ret;
    }

}
