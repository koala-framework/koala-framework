<?php
class Vpc_Advanced_Team_Member_Image_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlVps('default'),
                'width' => 90,
                'height' => 120,
                'scale' => Vps_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}
