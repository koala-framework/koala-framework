<?php
class Kwc_Blog_Category_ShowCategories_Component extends Kwc_Directories_Category_ShowCategories_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Blog.Show categories');
        $ret['componentIcon'] = new Kwf_Asset('newspaper');
        $ret['showDirectoryClass'] = 'Kwc_Blog_Directory_Component';
        $ret['hideDirectoryClasses'] = array('Kwc_Events_Directory_Component');
        return $ret;
    }
}
