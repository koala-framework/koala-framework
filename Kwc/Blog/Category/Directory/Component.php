<?php
class Kwc_Blog_Category_Directory_Component extends Kwc_Directories_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Kwc_Blog_Category_Detail_Component';
        $ret['categoryToItemModelName'] = 'Kwc_Blog_Category_Directory_BlogPostsToCategoriesModel';
        return $ret;
    }
}
