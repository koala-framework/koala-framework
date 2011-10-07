<?php
class Vpc_News_Category_Directory_Component extends Vpc_Directories_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['categoryToItemModelName'] = 'Vpc_News_Category_Directory_NewsToCategoriesModel';
        return $ret;
    }
}
