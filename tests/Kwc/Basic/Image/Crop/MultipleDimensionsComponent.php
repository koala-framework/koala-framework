<?php
class Kwc_Basic_Image_Crop_MultipleDimensionsComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_Crop_TestModel';
        $ret['dimensions'] = array(
            'original' => array(
                'width'=>0,
                'height'=>0
            ),
            'small' => array(
                'width' => 100,
                'height' => 100,
                'bestfit' => false,
            ),
            'medium' => array(
                'width' => 200,
                'height' => 200,
                'bestfit' => false,
            ),
            'large' => array(
                'width' => 300,
                'height' => 300,
                'bestfit' => false,
            ),
            'userWidth' => array(
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => 300,
                'bestfit' => false,
            ),
            'userHeight' => array(
                'width' => 300,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'bestfit' => false,
            ),
            'userSize' => array(
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'bestfit' => false,
            ),
        );
        return $ret;
    }
}
