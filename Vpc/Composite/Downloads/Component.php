<?php
class Vpc_Composite_Downloads_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $settings = parent::getSettings();
        $settings['generators']['child']['component'] = 'Vpc_Basic_Download_Component';
        $settings['componentName'] = trlVps('Downloads');
        $settings['childModel'] = 'Vpc_Composite_Downloads_Model';

        return $settings;
    }
}
