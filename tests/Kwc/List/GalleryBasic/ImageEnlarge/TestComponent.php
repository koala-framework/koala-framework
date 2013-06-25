<?php
class Kwc_List_GalleryBasic_ImageEnlarge_TestComponent extends Kwc_List_Gallery_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Composite_ImagesEnlarge_ImageEnlarge_EnlargeTag_TestComponent';
        $ret['ownModel'] = 'Kwc_List_GalleryBasic_ImageEnlarge_TestModel';
        return $ret;
    }
}
