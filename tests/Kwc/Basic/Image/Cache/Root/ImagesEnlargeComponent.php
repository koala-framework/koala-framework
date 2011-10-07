<?php
class Vpc_Basic_Image_Cache_Root_ImagesEnlargeComponent extends Vpc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Basic_Image_Cache_Root_ImageEnlargeComponent';
        $ret['childModel'] = 'Vpc_Basic_Image_Cache_Root_ListModel';
        $ret['ownModel'] = 'Vpc_Basic_Image_Cache_Root_ListOwnModel';
        return $ret;
    }
}
