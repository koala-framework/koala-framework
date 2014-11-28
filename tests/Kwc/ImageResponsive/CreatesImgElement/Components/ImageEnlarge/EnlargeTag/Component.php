<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_EnlargeTag_Component
    extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_ImageEnlarge_EnlargeTag_Model';
        $ret['dimension'] = array('width'=>800, 'height'=>600, 'cover' => true);
        return $ret;
    }
}
