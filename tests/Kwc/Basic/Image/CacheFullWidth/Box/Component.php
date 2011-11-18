<?php
class Kwc_Basic_Image_CacheFullWidth_Box_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_CacheFullWidth_Box_TestModel';
        return $ret;
    }
}
