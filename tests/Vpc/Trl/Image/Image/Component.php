<?php
class Vpc_Trl_Image_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_Image_Image_TestModel';

        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlVps('default'),
                'width' => 120,
                'height' => 120,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            )
        );
        return $ret;
    }
}
