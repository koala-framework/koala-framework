<?php
class Vpc_Composite_ImagesEnlarge_Component extends Vpc_Composite_Images_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['childComponentClasses']['child'] = 'Vpc_Basic_Image_Enlarge_Title_Component';
        $settings['componentName'] = 'Images Enlarge';
        $settings['assets']['files'][] = 'vps/Vpc/Composite/ImagesEnlarge/Component.js';
        $settings['assets']['dep'][] = 'ExtCore';
        return $settings;
    }
}
