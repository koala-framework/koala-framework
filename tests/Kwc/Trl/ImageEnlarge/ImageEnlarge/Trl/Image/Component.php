<?php
class Kwc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_Component
    extends Kwc_Abstract_Image_Trl_Image_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_TestModel';
        return $ret;
    }
}
