<?php
class Vpc_Trl_NewsCategories_News_Category_Component extends Vpc_NewsCategory_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_NewsCategories_News_Category_CategoriesTestModel';
        $ret['categoryToItemModelName'] = 'Vpc_Trl_NewsCategories_News_Category_NewsToCategoriesTestModel';
        return $ret;
    }
}
