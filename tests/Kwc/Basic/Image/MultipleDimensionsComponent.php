<?php
class Kwc_Basic_Image_MultipleDimensionsComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['dimensions'] = array(
            'original' => array(
                'width'=>0,
                'height'=>0
            ),
            'small' => array(
                'width' => 100,
                'height' => 100,
                'cover' => true,
            ),
            'medium' => array(
                'width' => 200,
                'height' => 200,
                'cover' => true,
            ),
            'large' => array(
                'width' => 300,
                'height' => 300,
                'cover' => true,
            ),
            'userWidth' => array(
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => 300,
                'cover' => true,
            ),
            'userHeight' => array(
                'width' => 300,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'cover' => true,
            ),
            'userSize' => array(
                'width' => Kwc_Abstract_Image_Component::USER_SELECT,
                'height' => Kwc_Abstract_Image_Component::USER_SELECT,
                'cover' => true,
            ),
        );
        return $ret;
    }
}
