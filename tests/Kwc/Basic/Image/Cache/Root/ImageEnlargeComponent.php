<?php
class Kwc_Basic_Image_Cache_Root_ImageEnlargeComponent extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_Image_Cache_Root_EnlargeTagComponent';
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        return $ret;
    }
}
