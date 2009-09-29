<?php
class Vpc_Composite_ImagesEnlarge_ImageEnlarge_EnlargeTag_TestComponent extends Vpc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['alternativePreviewImage'] = false;
        $ret['ownModel'] = 'Vpc_Composite_ImagesEnlarge_ImageEnlarge_TestModel';
        return $ret;
    }
}
