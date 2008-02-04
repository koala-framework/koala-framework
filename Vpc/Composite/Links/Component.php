<?php
class Vpc_Composite_Links_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['childComponentClasses']['child'] = 'Vpc_Basic_Link_Component';
        $settings['componentName'] = 'Links';

        return $settings;
    }
}
