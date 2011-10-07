<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_Component extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] =
            'Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Component';
        $ret['ownModel'] = 'Vpc_Trl_ImageEnlarge_ImageEnlarge_TestModel';

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
