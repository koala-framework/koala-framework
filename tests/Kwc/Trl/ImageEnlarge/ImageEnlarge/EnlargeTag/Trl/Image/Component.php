<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_Component
    extends Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Image_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel';
        return $ret;
    }
}
