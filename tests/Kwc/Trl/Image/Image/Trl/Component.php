<?php
class Kwc_Trl_Image_Image_Trl_Component extends Kwc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Kwc_Trl_Image_Image_Trl_Image_Component.'.$masterComponentClass;
        $ret['ownModel'] = 'Kwc_Trl_Image_Image_Trl_TestModel';
        return $ret;
    }
}
