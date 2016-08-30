<?php
class Kwc_Directories_CategoryTest_Category_Directory_Component extends Kwc_Directories_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['component'] = 'Kwc_Directories_CategoryTest_Category_Detail_Component';
        $ret['categoryToItemModelName'] = 'Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel';
        $ret['childModel'] = 'Kwc_Directories_CategoryTest_Category_Directory_CategoriesModel';
        return $ret;
    }
}
