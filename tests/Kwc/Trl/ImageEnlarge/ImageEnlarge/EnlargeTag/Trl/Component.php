<?php
class Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Component
    extends Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_TestModel';
        return $ret;
    }
}
