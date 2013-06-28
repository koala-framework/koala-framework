<?php
class Kwc_List_ImagesLinked_Image_Component extends Kwc_List_Gallery_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] =
            'Kwc_TextImage_ImageEnlarge_LinkTag_Component';
        return $ret;
    }
}
