<?php
class Vpc_Trl_Image_Image_Trl_Component extends Vpc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Vpc_Trl_Image_Image_Trl_Image_Component.'.$masterComponentClass;
        $ret['ownModel'] = 'Vpc_Trl_Image_Image_Trl_TestModel';
        return $ret;
    }
}
