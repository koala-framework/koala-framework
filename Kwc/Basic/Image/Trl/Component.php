<?php
class Kwc_Basic_Image_Trl_Component extends Kwc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['apiContent'] = 'Kwc_Basic_Image_Trl_ApiContent';
        $ret['apiContentType'] = 'image';
        return $ret;
    }
}
