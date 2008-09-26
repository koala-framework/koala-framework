<?php
class Vpc_Events_Category_ShowCategories_Component extends Vpc_News_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Events.Show categories');
        $ret['showDirectoryClass'] = 'Vpc_Events_Directory_Component';
        $ret['hideDirectoryClasses'] = array();
        return $ret;
    }
}
