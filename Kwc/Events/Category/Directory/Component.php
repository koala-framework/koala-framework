<?php
class Kwc_Events_Category_Directory_Component extends Kwc_News_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_Events_Category_Directory_EventsToCategoriesModel';
        return $ret;
    }
}
