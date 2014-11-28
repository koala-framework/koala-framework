<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component
    extends Kwc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel';
        $ret['dimension'] = array('width'=>800, 'height'=>600, 'cover' => true);
        return $ret;
    }
}
