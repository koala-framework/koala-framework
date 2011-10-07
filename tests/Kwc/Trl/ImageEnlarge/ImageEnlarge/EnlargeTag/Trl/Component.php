<?php
class Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Component
    extends Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_Component.'.$masterComponentClass;
        $ret['ownModel'] = 'Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_TestModel';
        return $ret;
    }
}
