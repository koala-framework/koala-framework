<?php
class Kwc_Articles_CategorySimple_Component extends Kwc_Directories_CategorySimple_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_Articles_CategorySimple_CategoriesToItemsModel';
        return $ret;
    }
}
