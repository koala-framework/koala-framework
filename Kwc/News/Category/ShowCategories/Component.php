<?php
class Kwc_News_Category_ShowCategories_Component extends Kwc_Directories_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('News.Show categories');
        $ret['componentIcon'] = 'newspaper';
        $ret['showDirectoryClass'] = 'Kwc_News_Directory_Component';
        $ret['hideDirectoryClasses'] = array('Kwc_Events_Directory_Component');
        return $ret;
    }
}
