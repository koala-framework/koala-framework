<?php
class Kwc_Articles_Detail_PreviewImage_Mail_Component extends Kwc_Basic_ImageParent_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        $ret['dimension'] = array(
            'text' => trlKwfStatic('default'),
            'width' => 230,
            'height' => 0,
            'cover' => true,
        );
        return $ret;
    }
}
