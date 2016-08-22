<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_Component extends
    Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_EnlargeTag_Component';
        return $ret;
    }
}
