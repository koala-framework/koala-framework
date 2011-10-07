<?php
class Kwc_Composite_ImagesEnlarge_ImageEnlarge_TestComponent extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Composite_ImagesEnlarge_ImageEnlarge_EnlargeTag_TestComponent';
        $ret['ownModel'] = 'Kwc_Composite_ImagesEnlarge_ImageEnlarge_TestModel';
        $ret['dimensions'] = array(
            array('width'=>100, 'height'=>100, 'scale'=>Kwf_Media_Image::SCALE_CROP)
        );
        return $ret;
    }
}
