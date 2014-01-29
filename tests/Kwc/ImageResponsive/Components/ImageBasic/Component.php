<?php
class Kwc_ImageResponsive_Components_ImageBasic_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_Components_Image_TestModel';
        return $ret;
    }
}
