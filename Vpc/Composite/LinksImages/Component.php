<?php
class Vpc_Composite_LinksImages_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['generators']['child']['component'] = 'Vpc_Composite_LinkImage_Component';
        $settings['componentName'] = trlVps('Links Images');
        $settings['componentIcon'] = new Vps_Asset('images');
        $settings['tablename'] = 'Vpc_Composite_LinksImages_Model';

        return $settings;
    }
}
