<?php
class Kwc_Articles_Category_ShowCategories_Component extends Kwc_Directories_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Articles') . '.' . trlKwfStatic('Show categories');
        $ret['componentIcon'] = 'newspaper';
        $ret['showDirectoryClass'] = 'Kwc_Articles_Directory_Component';
        return $ret;
    }
}
