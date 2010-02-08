<?php
class Vpc_Basic_Image_Cache_Root_ImageEnlargeComponent extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Basic_Image_Cache_Root_EnlargeTagComponent';
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        return $ret;
    }
}
