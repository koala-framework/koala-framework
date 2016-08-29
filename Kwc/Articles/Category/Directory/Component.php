<?php
class Kwc_Articles_Category_Directory_Component extends Kwc_Directories_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_Articles_Category_Directory_ArticlesToCategoriesModel';

        $ret['menuConfig'] = 'Kwc_Articles_Category_Directory_MenuConfig';
        return $ret;
    }
}
