<?php
class Kwc_Trl_NewsCategories_News_Category_Component extends Kwc_NewsCategory_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Trl_NewsCategories_News_Category_CategoriesTestModel';
        $ret['categoryToItemModelName'] = 'Kwc_Trl_NewsCategories_News_Category_NewsToCategoriesTestModel';
        return $ret;
    }
}
