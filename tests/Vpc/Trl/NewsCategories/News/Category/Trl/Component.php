<?php
class Vpc_Trl_NewsCategories_News_Category_Trl_Component extends Vpc_Directories_Category_Directory_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['childModel'] = 'Vpc_Trl_NewsCategories_News_Category_Trl_CategoriesTestModel';
        return $ret;
    }
}
