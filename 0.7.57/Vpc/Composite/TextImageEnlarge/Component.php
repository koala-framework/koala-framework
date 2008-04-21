<?php
class Vpc_Composite_TextImageEnlarge_Component extends Vpc_Composite_TextImage_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['componentName'] = trlVps('Text Image enlarge');
        $settings['childComponentClasses']['image'] =
                                        'Vpc_Basic_Image_Enlarge_Component';
        return $settings;
    }
}
