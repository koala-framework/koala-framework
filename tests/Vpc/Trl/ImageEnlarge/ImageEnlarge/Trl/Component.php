<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_Component extends Vpc_Basic_ImageEnlarge_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_Component.'.$masterComponentClass;
        $ret['ownModel'] = 'Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_TestModel';
        return $ret;
    }
}
