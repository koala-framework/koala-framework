<?php
class Vpc_Basic_ImagePosition_Image_TestComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_ImagePosition_Image_TestModel';
        $ret['dimensions'] = array(array('width'=>100, 'height'=>100, 'scale'=>Vps_Media_Image::SCALE_BESTFIT));
        return $ret;
    }
}
