<?php
class Vpc_Trl_Image_Image_Trl_Image_Component
    extends Vpc_Abstract_Image_Trl_Image_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Vpc_Trl_Image_Image_Trl_Image_TestModel';
        return $ret;
    }
}
