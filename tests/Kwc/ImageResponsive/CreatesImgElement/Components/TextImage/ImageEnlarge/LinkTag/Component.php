<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_Component extends Kwc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_Model';
        $ret['generators']['child']['component'] = array(
            'none' => 'Kwc_Basic_LinkTag_Empty_Component',
            'download' => 'Kwc_Basic_LinkTag_Empty_Component',
            'enlarge' => 'Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component'
        );
        return $ret;
    }
}
