<?php
class Kwc_List_GalleryBasic_ImageEnlarge_EnlargeTag_TestComponent extends Kwc_List_Gallery_Image_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_List_GalleryBasic_ImageEnlarge_TestModel';
        return $ret;
    }
}
