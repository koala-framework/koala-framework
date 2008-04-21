<?php
class Vpc_Composite_Images_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();

        $settings['componentName'] = trlVps('Images');
        $settings['componentIcon'] = new Vps_Asset('pictures');
        $settings['tablename'] = 'Vpc_Composite_Images_Model';

        return $settings;
    }
}
