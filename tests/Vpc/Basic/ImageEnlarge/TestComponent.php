<?php
class Vpc_Basic_ImageEnlarge_TestComponent extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Basic_ImageEnlarge_EnlargeTag_TestComponent';
        $ret['dimensions'] = array(
            array('width'=>10, 'height'=>10, 'scale'=>Vps_Media_Image::SCALE_DEFORM)
        );
        $ret['ownModel'] = 'Vpc_Basic_ImageEnlarge_TestModel';
        return $ret;
    }
}
