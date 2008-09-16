<?php
class Vpc_Box_LinksImages_LinkImage_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(0, 0, Vps_Media_Image::SCALE_ORIGINAL);
        return $ret;
    }
}
