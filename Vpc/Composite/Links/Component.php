<?php
class Vpc_Composite_Links_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['childComponentClasses']['child'] = 'Vpc_Basic_Link_Component';
        $settings['componentName'] = 'Links';
        $settings['componentIcon'] = new Vps_Asset('links');
        $settings['tablename'] = 'Vpc_Composite_Links_Model';

        return $settings;
    }
}
