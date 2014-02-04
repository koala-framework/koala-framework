<?php
class Kwc_ImageResponsive_Components_ImageEnlarge_Component extends
    Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_Components_Image_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_ImageResponsive_Components_ImageEnlarge_EnlargeTag_Component';
        return $ret;
    }
}
