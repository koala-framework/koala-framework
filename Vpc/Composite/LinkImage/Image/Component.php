<?php
class Vpc_Composite_LinkImage_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['dimension'] = array(150, 0);
        return $settings;
    }
}