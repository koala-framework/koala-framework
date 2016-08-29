<?php
class Kwc_Events_Category_ShowCategories_Component extends Kwc_News_Category_ShowCategories_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Events.Show categories');
        $ret['showDirectoryClass'] = 'Kwc_Events_Directory_Component';
        $ret['hideDirectoryClasses'] = array();
        return $ret;
    }
}
