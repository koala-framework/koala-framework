<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_Component extends Kwc_TextImage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_Model';
        $ret['generators']['child']['component']['text'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_Component';
        return $ret;
    }
}
