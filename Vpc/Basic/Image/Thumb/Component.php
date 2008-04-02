<?php
class Vpc_Basic_Image_Thumb_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['dimension'] = array(100, 75);
        $settings['scale'] = array(Vps_Media_Image::SCALE_CROP);
        return $settings;
    }
}