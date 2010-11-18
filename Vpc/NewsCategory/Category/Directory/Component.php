<?php
class Vpc_NewsCategory_Category_Directory_Component extends Vpc_News_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['categoryToItemModelName'] = 'Vpc_NewsCategory_Category_Directory_NewsToCategoriesModel';
        return $ret;
    }
}
