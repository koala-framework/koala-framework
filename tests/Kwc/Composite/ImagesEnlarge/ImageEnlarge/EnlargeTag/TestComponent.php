<?php
class Kwc_Composite_ImagesEnlarge_ImageEnlarge_EnlargeTag_TestComponent extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['alternativePreviewImage'] = false;
        $ret['ownModel'] = 'Kwc_Composite_ImagesEnlarge_ImageEnlarge_TestModel';
        return $ret;
    }
}
