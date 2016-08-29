<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_ImageBasic_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel';
        return $ret;
    }
}
