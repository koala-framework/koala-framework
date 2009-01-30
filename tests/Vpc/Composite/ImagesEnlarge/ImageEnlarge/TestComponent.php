<?php
class Vpc_Composite_ImagesEnlarge_ImageEnlarge_TestComponent extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Composite_ImagesEnlarge_ImageEnlarge_EnlargeTag_TestComponent';
        $ret['modelname'] = 'Vpc_Composite_ImagesEnlarge_ImageEnlarge_TestModel';
        $ret['dimensions'] = array(
            array('width'=>100, 'height'=>100, 'scale'=>Vps_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
