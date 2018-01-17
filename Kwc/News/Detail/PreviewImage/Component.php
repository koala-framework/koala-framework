<?php
class Kwc_News_Detail_PreviewImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimensions'] = array(
            array(
                'width' => 120,
                'height' => 90,
                'cover' => false,
            )
        );
        $ret['defineWidth'] = true;
        return $ret;
    }
}
