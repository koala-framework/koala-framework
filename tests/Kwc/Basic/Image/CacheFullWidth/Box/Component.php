<?php
class Kwc_Basic_Image_CacheFullWidth_Box_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Basic_Image_CacheFullWidth_Box_TestModel';
        return $ret;
    }
}
