<?php
class Kwc_ArticlesCategory_Category_Directory_Component extends Kwc_Articles_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_ArticlesCategory_Category_Directory_ArticlesToCategoriesModel';
        $ret['generators']['detail']['component'] = 'Kwc_ArticlesCategory_Category_Detail_Component';
        return $ret;
    }
}
