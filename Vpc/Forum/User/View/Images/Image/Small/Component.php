<?php
class Vpc_Forum_User_View_Images_Image_Small_Component extends Vpc_Basic_Image_Enlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimension'] = array(100, 75);
        $ret['scale'] = array(Vps_Media_Image::SCALE_CROP);
        return $ret;
    }

}
