<?php
class Kwc_News_Category_Directory_Component extends Kwc_Directories_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_News_Category_Directory_NewsToCategoriesModel';
        return $ret;
    }
}
