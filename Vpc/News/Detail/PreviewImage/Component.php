<?php
class Vpc_News_Detail_PreviewImage_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dimensions'         => array(30, 20),
        ));
        return $ret;
    }
}
