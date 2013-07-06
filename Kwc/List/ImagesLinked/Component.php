<?php
class Kwc_List_ImagesLinked_Component extends Kwc_List_Gallery_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_List_ImagesLinked_Image_Component';
        return $ret;
    }
}
