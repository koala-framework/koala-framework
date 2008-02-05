<?php
class Vpc_Composite_Images_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        
        $settings['childComponentClasses']['child'] = 'Vpc_Basic_Image_Enlarge_Component';
        $settings['componentName'] = 'Images';
        $settings['componentIcon'] = new Vps_Asset('pictures');

        return $settings;
    }
}
