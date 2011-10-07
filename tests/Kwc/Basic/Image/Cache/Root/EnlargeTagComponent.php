<?php
class Kwc_Basic_Image_Cache_Root_EnlargeTagComponent extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        return $ret;
    }

}
