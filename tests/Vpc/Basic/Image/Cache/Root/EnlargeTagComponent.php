<?php
class Vpc_Basic_Image_Cache_Root_EnlargeTagComponent extends Vpc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        return $ret;
    }

}
