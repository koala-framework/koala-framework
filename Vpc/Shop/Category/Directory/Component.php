<?php
class Vpc_Shop_Category_Directory_Component extends Vpc_Directories_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pool'] = 'productcategories';
        $ret['categoryToItemModelName'] = 'Vpc_Shop_Category_Directory_ProductsToCategoriesModel';
        return $ret;
    }
}
