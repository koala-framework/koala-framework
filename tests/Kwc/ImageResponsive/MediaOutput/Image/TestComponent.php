<?php
class Kwc_ImageResponsive_MediaOutput_Image_TestComponent extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_MediaOutput_Image_TestModel';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 200,
                'height' => 200,
                'cover' => true
            ),
            'default2'=>array(
                'text' => 'Default2',
                'width' => 100,
                'height' => 100,
                'cover' => true
            )
        );
        return $ret;
    }
}
