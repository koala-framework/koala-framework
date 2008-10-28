<?php
class Vpc_Basic_ImageEnlarge_TestComponent extends Vpc_Basic_Image_Enlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_ImageEnlarge_TestModel';
        $ret['generators']['smallImage']['component'] = 'Vpc_Basic_ImageEnlarge_SmallImage_Component';
        $ret['dimensions'] = array(16, 16, Vps_Media_Image::SCALE_DEFORM);
        return $ret;
    }
}
