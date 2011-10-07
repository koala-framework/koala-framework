<?php
class Vpc_Basic_Image_EmptyImageComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        $ret['emptyImage'] = 'empty.png';
        $ret['dimensions'] = array(array('width'=>16, 'height'=>16, 'scale'=>Vps_Media_Image::SCALE_DEFORM));
        return $ret;
    }
}
