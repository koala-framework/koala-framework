<?php
class Vpc_Basic_Image_MultipleDimensionsComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(
            'original' => array(
                'scale' => Vps_Media_Image::SCALE_ORIGINAL,
                'width'=>0,
                'height'=>0
            ),
            'small' => array(
                'width' => 100,
                'height' => 100,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'medium' => array(
                'width' => 200,
                'height' => 200,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'large' => array(
                'width' => 300,
                'height' => 300,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'userWidth' => array(
                'width' => Vpc_Abstract_Image_Component::USER_SELECT,
                'height' => 300,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'userHeight' => array(
                'width' => 300,
                'height' => Vpc_Abstract_Image_Component::USER_SELECT,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
            'userSize' => array(
                'width' => Vpc_Abstract_Image_Component::USER_SELECT,
                'height' => Vpc_Abstract_Image_Component::USER_SELECT,
                'scale' => Vps_Media_Image::SCALE_DEFORM
            ),
        );
        return $ret;
    }
}
