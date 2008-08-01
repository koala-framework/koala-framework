<?php
class Vpc_News_Category_ShowCategories_Component extends Vpc_Directories_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.Show categories');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['showDirectoryClass'] = 'Vpc_News_Directory_Component';
        return $ret;
    }
}
