<?php
class Vpc_User_Detail_Avatar_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Avatar');
        $ret['dimensions'] = array(300, 300, Vps_Media_Image::SCALE_BESTFIT);
        return $ret;
    }
}
