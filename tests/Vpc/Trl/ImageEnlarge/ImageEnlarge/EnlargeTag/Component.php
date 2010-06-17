<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Component extends Vpc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel';
        $ret['dimensions'] = array(array(
            'width'=>null, 'height'=>null, 'scale'=>Vps_Media_Image::SCALE_ORIGINAL
        ));
        $ret['imageTitle'] = false;
        return $ret;
    }
}
